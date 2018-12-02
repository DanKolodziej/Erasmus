<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Student;
use AppBundle\Form\CourseStudentGradeType;
use AppBundle\Form\CourseStudentNoteType;
use AppBundle\Form\StudentUniversityType;
use AppBundle\Form\UserType;
use AppBundle\Form\StudentType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use FOS\UserBundle\Model\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class StudentController extends Controller {

    /**
     * @Route("/EnrolledStudentsSummary", name="EnrolledStudentsSummary")
     */
    public function EnrolledStudentsSummaryAction(){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $courseService = $this->get('course_service');
        $courses = $courseService->getAllCourses();

        $studentService = $this->get('student_service');
        $phpExcelObject = $studentService->getEnrolledStudentsSummaryAsExcel($courses, $user);

        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');

        $response = $this->get('phpexcel')->createStreamedResponse($writer);

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'EnrolledStudentsSummary.xlsx'
        );
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    /**
     * @Route("/studentList", name="studentList")
     */
    public function studentListAction(Request $request)
    {
        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorOrDeansOfficeAccess($user);

        $facultyId = $user->getFaculty();
        $facultyService = $this->get('faculty_service');
        $faculty = $facultyService->getFaculty($facultyId);
        $upperFacultyId = $faculty->getUpperFaculty();

        $facultyIds = array($facultyId);
        if(isset($upperFacultyId)){
            $facultyIds[] = $upperFacultyId;
        }

        $studentService = $this->get('student_service');
        $students = $studentService->getFacultyStudentUsers($facultyIds);
        $studentArr = $studentService->getFacultyStudents($facultyIds);
        $indexes = $studentService->getAllStudentsIndexes($studentArr);

        $student = new Student();

        $form = $this->createForm(StudentUniversityType::class, $student);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($student);
            $em->flush();
            $userId = $student->getUser();
            $user = $em->getRepository('AppBundle:User')->find($userId);

            if(isset($upperFacultyId)){

                $user->setFaculty($upperFacultyId);
            } else{

                $user->setFaculty($facultyId);
            }
            $user->setEnabled(1);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('studentList');
        }

        return $this->render('Student/studentList.html.twig', array('students' => $students, 'studentArr' => $studentArr ,'indexes' => $indexes,
            'form' => $form->createView(), 'formTitle' => 'Add Student'));
    }

    /**
     * @Route("/externalCoordinatorStudentList", name="externalCoordinatorStudentList")
     */
    public function externalCoordinatorStudentListAction(Request $request)
    {
        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->externalCoordinatorAccess($user);

        $externalCoordinatorService = $this->get('external_coordinator_service');
        $userId = $user->getId();
        $externalCoordinatorId = $externalCoordinatorService->getExternalCoordinatorByUserId($userId);

        $studentService = $this->get('student_service');
        $students = $studentService->getExternalCoordinatorStudentUsers($externalCoordinatorId);
        $studentArr = $studentService->getExternalCoordinatorStudents($externalCoordinatorId);
        $indexes = $studentService->getAllStudentsIndexes($studentArr);

        return $this->render('Student/externalCoordinatorOrDWMStudentList.html.twig', array('students' => $students, 'studentArr' => $studentArr ,'indexes' => $indexes));
    }

    /**
     * @Route("/dwmStudentList", name="dwmStudentList")
     */
    public function dwmStudentListAction(Request $request)
    {
        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->dwmAccess($user);

        $studentService = $this->get('student_service');
        $students = $studentService->getAllStudentUsers();
        $studentArr = $studentService->getAllStudents();
        $indexes = $studentService->getAllStudentsIndexes($studentArr);

        return $this->render('Student/externalCoordinatorOrDWMStudentList.html.twig', array('students' => $students, 'studentArr' => $studentArr ,'indexes' => $indexes));
    }

    /**
     * @Route("/addStudent", name="addStudent")
     */
    public function addStudentAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->addUser($user, $form);
            $studentService = $this->get('student_service');
            $studentService->addUserAsStudent($user);

            return $this->redirectToRoute('internalCoordinator');
        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Add Student'));
    }

    /**
     * @Route("/editStudent/{id}", name="editStudent")
     */
    public function editStudentAction(Request $request, $id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorOrDeansOfficeAccess($user);

        $studentService = $this->get('student_service');
        $student = $studentService->getStudentByUserId($id);

        $form = $this->createForm(StudentUniversityType::class, $student);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $userId = $student->getUser();
            $user = $em->getRepository('AppBundle:User')->find($userId);
            $userService->saveUser($user);
            $em->persist($student);
            $em->flush();

            $this->addFlash('notice', 'Student edited');

            return $this->redirectToRoute('studentList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit Student'));
    }

    /**
     * @Route("/deleteStudent/{id}", name="deleteStudent")
     */
    public function deleteStudentAction($id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorAccess($user);

        $student = $this->getDoctrine()->getRepository('AppBundle:Student')->findOneByUser($id);
        $user = $userService->getUser($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($student);
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('studentList');
    }

    /**
     * @Route("/studentList/{studentId}", name="studentCourseList")
     */
    public function studentCourseListAction(Request $request, $studentId){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $studentService = $this->get('student_service');
        $student = $studentService->getStudent($studentId);
        $user = $studentService->getStudentUser($student);

        $courseService = $this->get('course_service');

        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $role = $session->get('role');

        $subQuery = $em->createQuery('
        SELECT CONCAT(IDENTITY(c2.course),IDENTITY(c2.student),MAX(c2.date))
        FROM AppBundle:CourseStudent c2
        GROUP BY c2.course, c2.student')->getResult();

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('AppBundle:CourseStudent', 'c');

        $query = $em->createNativeQuery('
        SELECT c.*
        FROM course_student c
        WHERE c.student_id = :student_id
        AND CONCAT(c.course_id, c.student_id, c.date) IN 
	      (:max_date_course_student_ids)', $rsm)
            ->setParameter('student_id', $studentId)
            ->setParameter('max_date_course_student_ids', $subQuery);
        $studentCourses = $query->getResult();

        $courses = [];
        $studentCoursesStates = [];
        $studentCoursesStatesIds = [];
        foreach ($studentCourses as $studentCourse){
            $courses[] = $em->getRepository('AppBundle:Course')->find($studentCourse->getCourse());
            $studentCoursesStates[] = $studentCourse->getState()->getName();
            $studentCoursesStatesIds[] = $studentCourse->getState()->getId();
        }

        $transitions = $em->getRepository('AppBundle:Transition')->createQueryBuilder('t')
            ->where('t.person = :role AND t.fromState IN (:course_state)')
            ->setParameter('role', $role)
            ->setParameter('course_state', $studentCoursesStatesIds)
            ->getQuery()->getResult();

        $coursesTransitions = [];

        foreach($studentCourses as $studentCourse){

            $courseState = $studentCourse->getState();
            $toStates = [];
            foreach($transitions as $transition){

                if($transition->getFromState() == $courseState){

                    $toStates[] = $transition;
                }
            }

            $coursesTransitions[$studentCourse->getCourse()->getName()] = $toStates;
        }

        $gradeForm = $this->createForm(CourseStudentGradeType::class);
        $grades = $courseService->getStudentCoursesGrades($studentId);

        $noteForm = $this->createForm(CourseStudentNoteType::class);
        $notes = $courseService->getStudentCoursesNotes($studentId);

        return $this->render('Coordinator/studentCourseList.html.twig', array('student' => $user, 'courses' => $courses, 'studentId' => $studentId,
            'studentCoursesStates' => $studentCoursesStates, 'coursesTransitions' => $coursesTransitions, 'transitions' => $transitions,
            'gradeFormObject' => $gradeForm, 'grades' => $grades, 'noteFormObject' => $noteForm, 'notes' => $notes));
    }

    /**
     * @Route("/assignStudentIndex/{studentId}", name="assignStudentIndex")
     */
    public function assignStudentIndexAction(Request $request, $studentId) {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorOrDWMAccess($user);

        $studentService = $this->get('student_service');
        $student = $studentService->getStudent($studentId);

        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            $studentService->assignIndex($student, $form);

            $listType = 'studentList';

            if($userService->isDWM($user)){

                $listType = 'dwmStudentList';
            }

            return $this->redirectToRoute($listType);
        }

        return $this->render('Coordinator/assignStudentIndex.html.twig', array('form' => $form->createView(), 'student' => $student));
    }

    /**
     * @Route("/studentList/addStudentCourse/{studentId}/{courseCode}", name="addStudentCourse")
     */
    public function addStudentCourseAction(Request $request, $studentId, $courseCode){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorOrDeansOfficeAccess($user);

        $studentService = $this->get('student_service');
        $student = $studentService->getStudent($studentId);

        $courseService = $this->get('course_service');
        $course = $courseService->getCourseByCode($courseCode);

        //$studentService->addCourseForStudent($student, $course);
        //$courseService->addStudentForCourse($course, $student);

        $courseService->addStudentCourseEnrollment($course, $student, $user);

        return $this->redirectToRoute('studentCourseList', array('studentId' => $studentId));
    }

    /**
     * @Route("/studentList/deleteStudentCourse/{studentId}/{courseCode}", name="deleteStudentCourse")
     */
    public function deleteStudentCourseAction($studentId, $courseCode){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorAccess($user);

        $studentService = $this->get('student_service');
        $student = $studentService->getStudent($studentId);

        $courseService = $this->get('course_service');
        $course = $courseService->getCourseByCode($courseCode);

        //$studentService->deleteCourseFromStudent($student, $course);
        //$courseService->deleteStudentFromCourse($course, $student);

        $courseService->deleteStudentCourseEnrollment($course, $student);

        return $this->redirectToRoute('studentCourseList', array('studentId' => $studentId));
    }

    /**
     * @Route("/studentList/addStudentCourseGrade/{studentId}/{courseCode}", name="addStudentCourseGrade")
     */
    public function addStudentCourseGrade(Request $request, $studentId, $courseCode){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $studentService = $this->get('student_service');
        $student = $studentService->getStudent($studentId);

        $gradeForm = $this->createForm(CourseStudentGradeType::class);

        $gradeForm->handleRequest($request);
        if($gradeForm->isSubmitted() && $gradeForm->isValid()){

            $grade = $gradeForm->get('grade')->getData();

            $courseService = $this->get('course_service');
            $course = $courseService->getCourseByCode($courseCode);
            $courseService->addStudentCourseGrade($course->getId(), $studentId, $grade);
        }

        return $this->redirectToRoute('studentCourseList', array('studentId' => $studentId));
    }

    /**
     * @Route("/studentList/addStudentCourseNote/{studentId}/{courseCode}", name="addStudentCourseNote")
     */
    public function addStudentCourseNote(Request $request, $studentId, $courseCode){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $studentService = $this->get('student_service');
        $student = $studentService->getStudent($studentId);

        $noteForm = $this->createForm(CourseStudentNoteType::class);

        $noteForm->handleRequest($request);
        if($noteForm->isSubmitted() && $noteForm->isValid()){

            $note = $noteForm->get('textNote')->getData();

            $courseService = $this->get('course_service');
            $course = $courseService->getCourseByCode($courseCode);
            $courseService->addStudentCourseNote($course->getId(), $studentId, $note);
        }

        return $this->redirectToRoute('studentCourseList', array('studentId' => $studentId));
    }
}