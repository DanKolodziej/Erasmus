<?php

namespace AppBundle\Service;

use AppBundle\Entity\Student;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class StudentService extends UserService {

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        parent::__construct($em, $container, $logger);
    }

    public function getStudentUser($student){

        $userId = $student->getUser();
        $user = $this->em->getRepository('AppBundle:User')->find($userId);

        return $user;
    }

    public function getStudent($studentId){

        return $this->em->getRepository('AppBundle:Student')->findOneBy(array('student_id' => $studentId));
    }

    public function addUserAsStudent($user){

        $student = new Student();
        $student->setUser($user);
        $this->saveStudent($student);
    }

    public function assignIndex($student, $form){

        $student->setIndexNumber($form["indexNumber"]->getData());
        $this->saveStudent($student);
    }

    public function saveStudent($student){

        if($student){

            $this->em->persist($student);
            $this->em->flush();
            $this->logger->info('Added new Student user: '.$student->getId());
        }
    }

    public function getExternalCoordinatorStudentUsers($externalCoordinatorId){

        $students = $this->getExternalCoordinatorStudents($externalCoordinatorId);

        $studentArr = $this->getStudentUsers($students);

        $repository = $this->em->getRepository('AppBundle:User');

        $query = $repository->createQueryBuilder('u')
            ->where('u.id IN (:student_ids)')
            ->setParameter('student_ids',$studentArr)
            ->getQuery();

        return $query->getResult();
    }

    public function getExternalCoordinatorStudents($externalCoordinatorId){

        return $this->em->getRepository('AppBundle:Student')->findByExternalCoordinator($externalCoordinatorId);
    }

    public function getStudentUsers($students){

        $studentUsers = [];

        foreach ($students as $student) {

            $studentUsers[] = $student->getUser();
        }

        return $studentUsers;
    }

    public function getFacultyStudents($facultyIds){

        $studentUsers = $this->getFacultyStudentUsers($facultyIds);

        $repository = $this->em->getRepository('AppBundle:Student');

        $query = $repository->createQueryBuilder('s')
            ->where('s.user IN (:students)')
            ->setParameter('students', $studentUsers)
            ->getQuery();

        return $query->getResult();
    }

    public function getFacultyStudentUsers($facultyIds){

        $studentArr = $this->getAllStudentUsers();

        $repository = $this->em->getRepository('AppBundle:User');

        $query = $repository->createQueryBuilder('u')
            ->where('u.faculty IN (:faculties) AND u.id IN (:student_ids)')
            ->setParameter('faculties', $facultyIds)
            ->setParameter('student_ids',$studentArr)
            ->getQuery();

        return $query->getResult();
    }

    public function getAllStudentUsers(){

        $students = $this->getAllStudents();
        $studentUsers = [];

        foreach ($students as $student) {

            $studentUsers[] = $student->getUser();
        }

        return $studentUsers;
    }

    public function getAllStudents(){

        return $this->em->getRepository('AppBundle:Student')->findAll();
    }

    public function getStudentByUserId($userId){

        return $this->em->getRepository('AppBundle:Student')->findOneByUser($userId);
    }

    public function getAllStudentsIndexes($students){

        $indexes = [];

        foreach($students as $student){

            $indexes[] = $student->getIndexNumber();
        }

        return $indexes;
    }

    public function getEnrolledStudentsSummaryAsExcel($courses, $user){

        $phpExcelObject = $this->createExcelFile();

        $fileCreator = $user->getUsername();
        $this->setExcelFileProperties($phpExcelObject, $fileCreator);

        $row = $this->getFirstRow();
        $this->createCourseDescriptionRows($phpExcelObject, $row, $courses);

        return $phpExcelObject;
    }

    public function createExcelFile(){

        return $this->container->get('phpexcel')->createPHPExcelObject();
    }

    public function setExcelFileProperties($phpExcelObject, $creator){

        $phpExcelObject->getProperties()->setCreator($creator)
            ->setLastModifiedBy("")
            ->setTitle("Enrolled Students Summary")
            ->setSubject("Office 2005 XLSX Test Document")
            ->setDescription("Test document for Office 2005 XLSX, generated using PHP classes.")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Test result file");
    }

    public function getFirstRow(){

        return 1;
    }

    public function createCourseDescriptionRows($phpExcelObject, $row, $courses){

        foreach($courses as $index => $course){

            $students = $this->container->get('course_service')->getCourseStudents($course);

            $this->setRowAsCourseTitle($phpExcelObject, $row);

            $row = $this->nextRow($row);

            $numberOfStudents = sizeof($students);

            $this->setRowAsCourseDescription($phpExcelObject, $row, $course, $numberOfStudents);

            $this->createStudentDescriptionRows($phpExcelObject, $row, $students);

            $row = $this->getNextCourseTitleRow($row, $numberOfStudents);
        }
    }

    public function setRowAsCourseTitle($phpExcelObject, $row){

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'COURSE')
            ->setCellValue('B'.$row, 'Name')
            ->setCellValue('C'.$row, 'Code')
            ->setCellValue('D'.$row, 'Number of students');

        $this->setFontAsBoldInCourseheading($phpExcelObject, $row);
    }

    public function setFontAsBoldInCourseHeading($phpExcelObject, $row){

        $phpExcelObject->getActiveSheet()->getStyle('A'.$row.':D'.$row)->getFont()->setBold(true);
    }

    public function nextRow($row){

        return $row + 1;
    }

    public function getNextCourseTitleRow($row, $numberOfStudents){

        return $row + $numberOfStudents + 4;
    }

    public function setRowAsCourseDescription($phpExcelObject, $row, $course, $numberOfStudents){

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, 'Course:')
            ->setCellValue('B'.$row, $course->getName())
            ->setCellValue('C'.$row, $course->getCode())
            ->setCellValue('D'.$row, $numberOfStudents);
    }

    public function createStudentDescriptionRows($phpExcelObject, $row, $students){

        foreach($students as $key => $student){

            $studentDescriptionRow = $row+$key+1;
            $this->setRowAsStudentDescription($phpExcelObject, $studentDescriptionRow, $student);
        }
    }

    public function setRowAsStudentDescription($phpExcelObject, $row, $student){

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A'.($row), 'Student:')
            ->setCellValue('B'.($row), $student->getUser()->getName())
            ->setCellValue('C'.($row), $student->getUser()->getSurName());
    }
}