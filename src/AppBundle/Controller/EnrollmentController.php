<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CourseStudent;
use AppBundle\Entity\StudentEnrolledCourse;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class EnrollmentController extends Controller {

    /**
     * @Route("/enrollments", name="enrollments")
     */
    public function enrollmentsAction(Request $request)
    {

        $facultyService = $this->get('faculty_service');
        $faculties = $facultyService->getAllMainFaculties();

        $form = $this->createFormBuilder()
            ->add('courses', TextareaType::class, array(
                'label' => false,
                'attr' => array(
                'class' => 't',
                    'rows' => 8,
                    'cols' => 70,
                    'readonly' => true
            )))
            ->add('submit', SubmitType::class, array(
                'label' => 'Submit chosen courses',
                'attr' => array(
                    'class' => 'btn btn-primary btn-lg addBtn'
                )
            ))
            ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $coursesAsString = $form->getData()['courses'];

            $ArrCoursesAsStrings = explode(';', $coursesAsString);

            //var_dump($ArrCoursesAsStrings);
            $courses = [];

            if(isset($coursesAsString)){
                foreach($ArrCoursesAsStrings as $courseAsString){

                    //var_dump(explode(', ', $courseAsString));
                    if($courseAsString !== ""){

                        $courseCode = explode(', ', $courseAsString)[1];
                        $courses[] = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneByCode($courseCode);
                    }
                }
            }

            $coursesAsString = serialize($courses);
            $coursesAsString = urlencode($coursesAsString);

            return $this->render("Enrollments/enrollmentSummary.html.twig", array('courses' => $courses, 'coursesAsString' => $coursesAsString));
        }

        return $this->render('Enrollments/enrollmentsCourseList.html.twig', array('faculties' => $faculties, 'form' => $form->createView()));
    }

    /**
     * @Route("/enrollmentsFaculty/{id}", name="enrollmentsFaculty")
     */
    public function enrollmentsFacultyAction(Request $request, $id){

        $facultyService = $this->get('faculty_service');
        $facultyName = $facultyService->getFacultyName($id);
        $facultyShortName = $facultyService->getFacultyShortName($id);

        $courseService = $this->get('course_service');
        $coursesPrevious = $courseService->getFacultySemesterCourses($id, 1);
        $coursesCurrent = $courseService->getFacultySemesterCourses($id, 2);
        $coursesNext = $courseService->getFacultySemesterCourses($id, 3);
        $coursesOther = $courseService->getFacultySemesterCourses($id, 4);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Semester');

        $previousSemester = $repository->find(1);
        $currentSemester = $repository->find(2);
        $nextSemester = $repository->find(3);
        $otherSemester = $repository->find(4);

        return $this->render("Enrollments/enrollmentsFacultyCourseList.html.twig", array('coursesPrevious' => $coursesPrevious, 'coursesCurrent' => $coursesCurrent,
            'coursesNext' => $coursesNext, 'coursesOther' => $coursesOther, "facultyName" => $facultyName, "facultyShortName" => $facultyShortName,
            'previousSemester' => $previousSemester, 'currentSemester' => $currentSemester, 'nextSemester' => $nextSemester, 'otherSemester' => $otherSemester/*,'form' => $form->createView()*/));
    }

    /**
     * @Route("/enrollments/summary", name="enrollmentsSummary")
     */
    public function enrollmentsSummaryAction(){

        $coursesAsString = $this->get('session')->get('courses');

        $ArrCoursesAsStrings = explode(',', $coursesAsString);

        $courses = [];

        if(isset($coursesAsString)){
            foreach($ArrCoursesAsStrings as $courseAsString){

                $courseCode = str_replace(' ECTS', '', explode(': ', $courseAsString)[1]);

                $courses[] = $this->getDoctrine()->getRepository('AppBundle:Course')->findOneByCode($courseCode);
            }
        }

        return $this->render("Enrollments/enrollmentSummary.html.twig", array('courses' => $courses));
    }

    /**
     * @Route("enrollments/sendToAcceptation", name="enrollmentsSendToAcceptation")
     */
    public function enrollmentsSendToAcceptationAction(Request $request){

        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $student = $em->getRepository('AppBundle:Student')->findOneByUser($user);
        $studentName = $user->getName();
        $studentSurname = $user->getSurname();
        $studentId = $student->getId();

        $courses = $request->request->get('courses');
        $courses = urldecode($courses);
        $courses = unserialize($courses);

        foreach($courses as $course){

            $course = $em->merge($course);

            $courseStudent = $em->getRepository('AppBundle:CourseStudent')->findOneBy(array('course' => $course, 'student' => $student));

            if($courseStudent == null){

                $courseStudent = new CourseStudent($course, $student);

                $courseStudent->setPerson($user);

                $state = $em->getRepository('AppBundle:State')->findOneByName('ST-Enr-Prop');
                $courseStudent->setState($state);

                $time = new \DateTime();
                $time->format('H:i:s \O\n Y-m-d');
                $courseStudent->setDate($time);

                $em->persist($courseStudent);
                $em->flush();
            }
        }

        $studentCourses = $em->getRepository('AppBundle:CourseStudent')->findByStudent($studentId);

        $courses = [];
        $studentCoursesStates = [];
        foreach ($studentCourses as $studentCourse){
            $courses[] = $em->getRepository('AppBundle:Course')->find($studentCourse->getCourse());
            $studentCoursesStates[] = $studentCourse->getState()->getName();
        }

        return $this->redirectToRoute("enrollmentsState");
//        $response = $this->forward('AppBundle:Enrollment:enrollmentsState');
//
//        return $response;
    }

    /**
     * @Route("enrollments/coursesAcceptance/{studentId}", name="coursesAcceptance")
     */
    public function enrolledCoursesAction($studentId){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorOrExternalCoordinatorAccess($user);

        $session = $this->get('session');
        $userRole = $session->get('role');

        $em = $this->getDoctrine()->getManager();
        $studentsEnrolledCourses = $em->getRepository('AppBundle:StudentEnrolledCourse')->findByStudent($studentId);

        $courses = [];
        foreach ($studentsEnrolledCourses as $studentsEnrolledCourse){
            $courses[] = $em->getRepository('AppBundle:Course')->find($studentsEnrolledCourse->getCourse());
        }

        $student = $em->getRepository('AppBundle:Student')->find($studentId);
        $studentUser = $em->getRepository('AppBundle:User')->find($student->getUser());

        return $this->render('Coordinator/coursesAcceptance.html.twig', array('courses' => $courses, 'student' => $studentUser, 'accepterRole' => $userRole));
    }

    /**
     * @Route("enrollments/state", name="enrollmentsState")
     */
    public function enrollmentsStateAction(Request $request){

        $user = $this->getUser();

        $session = $request->getSession();
        $role = $session->get('role');

        $em = $this->getDoctrine()->getManager();
        $userId = $user->getId();
        $student = $em->getRepository('AppBundle:Student')->findOneByUser($userId);
        $studentName = $user->getName();
        $studentSurname = $user->getSurname();
        $studentId = $student->getId();

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

        return $this->render("Enrollments/enrollmentState.html.twig", array('courses' => $courses, 'studentId' => $studentId , 'studentName' => $studentName, 'studentSurname' => $studentSurname, 'studentCoursesStates' => $studentCoursesStates, 'coursesTransitions' => $coursesTransitions));
    }

    /**
     * @Route("enrollments/state/{studentId}", name="studentEnrollmentsState")
     */
    public function studentEnrollmentsStateAction($studentId, Request $request){

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $role = $session->get('role');

        $student = $em->getRepository('AppBundle:Student')->find($studentId);
        $userId = $student->getUser();
        $studentUser = $em->getRepository('AppBundle:User')->find($userId);
        $studentName = $studentUser->getName();
        $studentSurname = $studentUser->getSurname();

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

        return $this->render("Enrollments/enrollmentState.html.twig", array('courses' => $courses, 'studentId' => $studentId, 'studentName' => $studentName, 'studentSurname' => $studentSurname, 'studentCoursesStates' => $studentCoursesStates, 'coursesTransitions' => $coursesTransitions, 'transitions' => $transitions));
    }

    /**
     * @Route("enrollments/state/changeCourseState/{studentId}/{courseId}/{stateName}", name="changeCourseState")
     */
    public function changeStudentsCourseStateAction(Request $request, $studentId, $courseId, $stateName){

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $session = $request->getSession();
        $role = $session->get('role');

        $student = $em->getRepository('AppBundle:Student')->find($studentId);
        $userId = $student->getUser();
        $studentUser = $em->getRepository('AppBundle:User')->find($userId);
        $studentName = $studentUser->getName();
        $studentSurname = $studentUser->getSurname();

        $StudentCourseToChange = $em->getRepository('AppBundle:CourseStudent')->findOneBy(array('student' => $studentId, 'course' => $courseId));
        $state = $em->getRepository('AppBundle:State')->findOneByName($stateName);

        $changedStudentCourse = clone $StudentCourseToChange;
        $changedStudentCourse->setState($state);

        $time = new \DateTime();
        $time->format('H:i:s \O\n Y-m-d');
        $changedStudentCourse->setDate($time);

        $changedStudentCourse->setPerson($user);

        $em->persist($changedStudentCourse);
        $em->flush();

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

        return $this->redirect($request->headers->get('referer'));
    }
}