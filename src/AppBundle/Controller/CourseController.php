<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use AppBundle\Form\CourseType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CourseController extends Controller {

    /**
     * @Route("/addCourses", name="addCourses")
     */
    public function addCoursesAction(Request $request){

        $user = $this->getUser();

        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $course = new Course();

        $form = $this->createForm(CourseType::class, $course);
        $form->add('save', SubmitType::class, array('label' => 'Add Course', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 20px')))
            ->add('saveAndEnd', SubmitType::class, array('label' => 'Add course and end', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 20px')));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $this->getUser();

            $courseService = $this->get('course_service');
            $courseService->addCourse($course, $user);

            $this->addFlash('notice', 'Course added');

            $nextAction = $form->get('saveAndEnd')->isClicked() ? 'coursesList' : 'addCourses';

            return $this->redirectToRoute($nextAction);

        }

        return $this->render('Coordinator/addCourses.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/editCourse/{courseCode}", name="editCourse")
     */
    public function editCourseAction(Request $request, $courseCode){

        $user = $this->getUser();

        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $courseService = $this->get('course_service');

        $course = $courseService->getCourseByCode($courseCode);

        if($course->getSyllabus() != null){

            $course->setSyllabus(
                new File($this->getParameter('files/syllabus').'/'.$course->getSyllabus())
            );
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left:20px')));

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $courseService->editCourse($course);

            $this->addFlash('notice', 'Course edited');

            return $this->redirectToRoute('coursesList');

        }

        return $this->render('Coordinator/editCourse.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/coursesList", name="coursesList")
     */
    public function listOfCoursesAction(Request $request){

        $user = $this->getUser();

        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $semesterService = $this->get('semester_service');
        $semesters = $semesterService->getAllSemesters();

        return $this->render('Coordinator/coursesList.html.twig', array('semesters' => $semesters));
    }

    /**
     * @Route("/coursesList/{semesterId}", name="coursesSemesterList")
     */
    public function semesterCoursesAction(Request $request, $semesterId){

        $user = $this->getUser();

        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $courseService = $this->get('course_service');

        $user = $this->getUser();
        $usersFaculty = $user->getFaculty();

        $facultyService = $this->get('faculty_service');
        $faculty = $facultyService->getFaculty($usersFaculty);
        $upperFacultyId = $faculty->getUpperFaculty();

        if(isset($upperFacultyId)){

            $usersFaculty = $upperFacultyId;
        }

        $semester = $this->getDoctrine()->getRepository('AppBundle:Semester')->find($semesterId);
        $courses = $semester->getCourses();

        $facultyCourses = $courseService->getFacultyCourses($courses, $usersFaculty);

        return $this->render('Coordinator/semesterCourses.html.twig', array('courses' => $facultyCourses, 'semester' => $semester));
    }

    /**
     * @Route("/deleteCourse/{courseCode}", name="deleteCourse")
     */
    public function deleteCourseAction($courseCode){

        $user = $this->getUser();

        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $courseService = $this->get('course_service');

        $courseService->deleteCourseByCode($courseCode);

        return $this->redirectToRoute('coursesList');
    }

    /**
     * @Route("/availableCoursesList/{studentId}", name="availableCoursesList")
     */
    public function availableCourseListAction($studentId){

        $user = $this->getUser();

        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $facultyService = $this->get('faculty_service');
        $faculties = $facultyService->getAllMainFaculties();

        return $this->render('Coordinator/availableCoursesList.html.twig', array('studentId' => $studentId, 'faculties' => $faculties));
    }

    /**
     * @Route("/availableCoursesFaculty/{facultyId}/{studentId}", name="availableCoursesFaculty")
     */
    public function availableCoursesFacultyAction($studentId, $facultyId){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $facultyService = $this->get('faculty_service');
        $facultyName = $facultyService->getFacultyName($facultyId);
        $facultyShortName = $facultyService->getFacultyShortName($facultyId);

        $courseService = $this->get('course_service');
        $coursesPrevious = $courseService->getFacultySemesterCourses($facultyId, 1);
        $coursesCurrent = $courseService->getFacultySemesterCourses($facultyId, 2);
        $coursesNext = $courseService->getFacultySemesterCourses($facultyId, 3);
        $coursesOther = $courseService->getFacultySemesterCourses($facultyId, 4);

        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:Semester');

        $previousSemester = $repository->find(1);
        $currentSemester = $repository->find(2);
        $nextSemester = $repository->find(3);
        $otherSemester = $repository->find(4);

        return $this->render("Coordinator/availableCoursesFaculty.html.twig", array('studentId' => $studentId, 'coursesPrevious' => $coursesPrevious, 'coursesCurrent' => $coursesCurrent,
            'coursesNext' => $coursesNext, 'coursesOther' => $coursesOther, "facultyName" => $facultyName, "facultyShortName" => $facultyShortName,
            'previousSemester' => $previousSemester, 'currentSemester' => $currentSemester, 'nextSemester' => $nextSemester, 'otherSemester' => $otherSemester));
    }

    /**
     * @Route("/courseSearcher/{searchPhrase}", name="searchCourse")
     */
    public function searchCourseActon($searchPhrase){

        $user = $this->getUser();

        $userService = $this->get('user_service');
        $userService->InternalCoordinatorOrDeansOfficeAccess($user);

        $courseService = $this->get('course_service');

        $courses = $courseService->getCourseByCodeOrName($searchPhrase);

        $response = new Response(json_encode($courses));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/CourseStudentStateHistory/{courseId}/{studentId}", name="courseStudentStateHistory")
     */
    public function getCourseStudentStateHistoryActon(Request $request, $courseId, $studentId){

        $em = $this->getDoctrine()->getManager();

        $courseStudents = $em->createQueryBuilder()
            ->select(array('c', 's', 'u'))
            ->from('AppBundle:CourseStudent', 'c')
            ->where('c.course = :course AND c.student = :student')
            ->join('AppBundle:State', 's', 'WITH', 'c.state = s.stateId')
            ->join('AppBundle:User', 'u', 'WITH', 'c.person = u.id')
            ->orderBy('c.date', 'DESC')
            ->setParameter('course', $courseId)
            ->setparameter('student', $studentId)
            ->getQuery()->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)->getArrayResult();

        $response = new Response(json_encode($courseStudents));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}