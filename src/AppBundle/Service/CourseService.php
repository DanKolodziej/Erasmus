<?php

namespace AppBundle\Service;

use AppBundle\Entity\CourseStudent;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\File\File;

class CourseService
{
    private $em;
    private $container;
    private $logger;

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function getFacultyCourses($courses, $faculty){

        $facultyCourses = [];

        foreach ($courses as $course){

            if($course->getFaculty() == $faculty){

                $facultyCourses[] = $course;
            }
        }

        return $facultyCourses;
    }

    public function addCourse($course, $user){

        $file = $course->getSyllabus();

        if($file != null){

            $fileName = $this->uploadSyllabus($file);

            $course->setSyllabus($fileName);
        }

        $internalCoordinator = $this->em->getRepository('AppBundle:InternalCoordinator')->findOneByUser($user->getId());
        $course->setInternalCoordinator($internalCoordinator);

        $facultyService = $this->container->get('faculty_service');
        $faculty = $this->em->getRepository('AppBundle:Faculty')->find($user->getFaculty());
        $upperFaculty  = $facultyService->getUpperFaculty($faculty);

        if(isset($upperFaculty)){

            $course->setFaculty($upperFaculty);
        } else {

            $course->setFaculty($faculty);
        }

        $this->saveCourse($course);
    }

    public function editCourse($course){

        $file = $course->getSyllabus();

        if($file != null){

            $fileName = $this->uploadSyllabus($file);

            $course->setSyllabus($fileName);
        }

        $this->saveCourse($course);
    }

    public function uploadSyllabus(File $file){

        $fileName = md5(uniqid()).'.'.$file->guessExtension();

        $file->move(
            $this->container->getParameter('files/syllabus'),
            $fileName
        );

        return $fileName;
    }

    public function saveCourse($course){

        if($course){

            $this->em->persist($course);
            $this->em->flush();

            $this->logger->info('Added/Edited Course: '.$course->getCode());
        }
    }

    public function deleteCourseByCode($courseCode){

        $course = $this->getCourseByCode($courseCode);
        $this->deleteCourseFromDB($course);
    }

    public function getCourseByCode($courseCode){

        return $this->em->getRepository('AppBundle:Course')->findOneByCode($courseCode);
    }

    public function deleteCourseFromDB($course){

        if($course){

            $this->em->remove($course);
            $this->em->flush();
            $this->logger->info('Deleted Course: '.$course->getCode());
        }
    }

    public function getAllCourses(){

        return $this->em->getRepository('AppBundle:Course')->findAll();
    }

    public function getFacultySemesterCourses($facultyId, $semesterId){

        $repository = $this->em
            ->getRepository('AppBundle:Course');

        $query = $repository->createQueryBuilder('c')
            ->join('c.semesters', 's')
            ->where('c.faculty = :faculty AND s.semesterId IN (:semester_id)')
            ->setParameter('faculty', $facultyId)
            ->setParameter('semester_id', array($semesterId))
            ->getQuery();

        return $query->getResult();
    }

    public function getCourseByCodeOrName($searchPhrase){

        $query = $this->em->createQuery(
            'SELECT c
        FROM AppBundle:Course c
        WHERE c.code LIKE :searchPhrase OR c.name LIKE :searchPhrase'
        )->setParameter('searchPhrase', '%'.$searchPhrase.'%');

        return $query->getArrayResult();
    }

    public function addStudentCourseEnrollment($course, $student, $user){

        if($this->getStudentCourseEnrollment($course, $student)){

            $this->logger->notice('Student: '.$student->getId().' already enrolled to course: '.$course->getCode());
        } else {

            $courseStudent = new CourseStudent($course, $student);

            $courseStudent->setPerson($user);

            $state = $this->em->getRepository('AppBundle:State')->findOneByName('LA-Enr-Prop');
            $courseStudent->setState($state);

            $time = new \DateTime();
            $time->format('H:i:s \O\n Y-m-d');
            $courseStudent->setDate($time);

            $this->saveStudentCourseEnrollment($courseStudent);
        }
    }

    public function saveStudentCourseEnrollment($courseStudent){

        if($courseStudent){

            $this->em->persist($courseStudent);
            $this->em->flush();
            $this->logger->info('Student: '.$courseStudent->getStudent().', course: '.
                $courseStudent->getCourse().', state: '.$courseStudent->getId());
        }
    }

    public function deleteStudentCourseEnrollment($course, $student){

        $coursesStudent = $this->getStudentCourseEnrollment($course, $student);

        foreach($coursesStudent as $courseStudent){

            if($courseStudent){

                $this->em->remove($courseStudent);
            }
        }
        $this->em->flush();

        $this->logger->info('Student: '.$student->getId().', course: '.$course->getCode().
        ' successfully deleted state');
    }

    public function getStudentCourseEnrollment($course, $student){

        return $this->em->getRepository('AppBundle:CourseStudent')->findBy(array('course' => $course, 'student' => $student));
    }

    public function getStudentCourses($studentId){

        $courseStudents = $this->em->getRepository('AppBundle:CourseStudent')->findBy(array("student" => $studentId));

        $courseIds = [];

        foreach($courseStudents as $courseStudent){
            $courseIds[] = $courseStudent->getCourse();
        }

        return $this->em->getRepository('AppBundle:Course')->findBy(array('courseId' => $courseIds), array('courseId' => 'ASC'));
    }

    public function getCourseStudentEnrollments($courseId){

        return $this->em->getRepository('AppBundle:CourseStudent')->findBy(array("course" => $courseId));
    }

    public function getCourseStudents($courseId){

        $courseStudents = $this->em->getRepository('AppBundle:CourseStudent')->findBy(array("course" => $courseId));

        $studentIds = [];

        foreach($courseStudents as $courseStudent){
            $studentIds[] = $courseStudent->getStudent();
        }

        return $this->em->getRepository('AppBundle:Student')->findBy(array('student_id' => $studentIds));
    }

    public function addStudentCourseGrade($course, $student, $grade){

        $courseStudents = $this->getStudentCourseEnrollment($course, $student);

        foreach ($courseStudents as $courseStudent) {

            $courseStudent->setGrade($grade);
            $this->saveStudentCourseEnrollment($courseStudent);
        }
//        $courseStudent->setGrade($grade);
//
//        $this->saveStudentCourseEnrollment($courseStudent);

        $this->logger->info('Successfully added grade for student: '.$student.' in course: '.$course);
    }

    public function addStudentCourseNote($course, $student, $note){

        $courseStudents = $this->getStudentCourseEnrollment($course, $student);

        foreach ($courseStudents as $courseStudent) {

            $courseStudent->setTextNote($note);
            $this->saveStudentCourseEnrollment($courseStudent);
        }
//        $courseStudent->setTextNote($note);
//
//        $this->saveStudentCourseEnrollment($courseStudent);

        $this->logger->info('Successfully added text note for student: '.$student.' in course: '.$course);
    }

    public function getStudentCoursesGrades($studentId){

        $courseStudents =  $this->em->getRepository('AppBundle:CourseStudent')->findBy(array("student" => $studentId));

        $grades = [];

        foreach($courseStudents as $courseStudent){

            $grades[] = $courseStudent->getGrade();
        }

        return $grades;
    }

    public function getStudentCoursesNotes($studentId){

        $studentCourses =  $this->em->getRepository('AppBundle:CourseStudent')->findBy(array("student" => $studentId));

        $notes = [];

        foreach($studentCourses as $studentCourse){

            $notes[] = $studentCourse->getTextNote();
        }

        return $notes;
    }

}