<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ExternalCoordinator;
use AppBundle\Form\ExternalCoordinatorType;
use AppBundle\Form\UserType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Doctrine\ORM\Query\ResultSetMapping;

class CoordinatorController extends Controller {

    /**
     * @Route("/internalCoordinator", name="internalCoordinator")
     */
    public function internalCoordinatorAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorAccess($user);

        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $role = $session->get('role');

        $facultyId = $user->getFaculty();
        $facultyService = $this->get('faculty_service');
        $faculty = $facultyService->getFaculty($facultyId);
        $upperFacultyId = $faculty->getUpperFaculty();

        $facultyIds = array($facultyId);
        if(isset($upperFacultyId)){
            $facultyIds[] = $upperFacultyId;
        }

        $transitions = $em->getRepository('AppBundle:Transition')->createQueryBuilder('t')
            ->where('t.person = :role')
            ->setParameter('role', $role)
            ->getQuery()->getResult();

        $fromStates = [];
        foreach($transitions as $transition){

            $fromStates[] = $transition->getFromState();
        }

        $subQuery = $em->createQuery('
        SELECT CONCAT(IDENTITY(c2.course),IDENTITY(c2.student),MAX(c2.date))
        FROM AppBundle:CourseStudent c2
        GROUP BY c2.course, c2.student')->getResult();

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('AppBundle:CourseStudent', 'c');

        $query = $em->createNativeQuery('
        SELECT c.*
        FROM course_student c
        WHERE c.state IN (:from_states)
        AND CONCAT(c.course_id, c.student_id, c.date) IN 
	      (:max_date_course_student_ids)', $rsm)
        ->setParameter('from_states', $fromStates)
        ->setParameter('max_date_course_student_ids', $subQuery);
        $studentsCourses = $query->getResult();

        $coursesTransitionsByFaculty = [];
        $studentCoursesStatesByFaculty = [];
        $studentUsersByFaculty = [];
        $studentsIdsByFaculty = [];
        $coursesByUserRoleFaculty = [];
        $coursesTransitionsByCourseFaculty = [];
        $studentCoursesStatesByCourseFaculty = [];
        $studentUsersByCourseFaculty = [];
        $studentsIdsByCourseFaculty = [];
        $coursesByFaculty = [];
        foreach($studentsCourses as $studentCourse){

            $student = $em->getRepository('AppBundle:Student')->find($studentCourse->getStudent());
            $studentUser = $em->getRepository('AppBundle:User')->find($student->getUser());

            $course = $em->getRepository('AppBundle:Course')->find($studentCourse->getCourse());

            $studentCourseState = $studentCourse->getState()->getName();

            $courseState = $studentCourse->getState();
            $toStates = [];

            if(in_array($studentUser->getFaculty(), $facultyIds)){

                $studentsIdsByFaculty[] = $student->getId();
                $studentUsersByFaculty[] = $studentUser;
                $coursesByUserRoleFaculty[] = $course;
                $studentCoursesStatesByFaculty[] = $studentCourseState;

                foreach($transitions as $transition){

                    if($transition->getFromState() == $courseState){

                        $toStates[] = $transition;
                    }
                }
                $coursesTransitionsByFaculty[$studentCourse->getCourse()->getName()] = $toStates;
            } elseif(in_array($course->getFaculty(), $facultyIds)){

                $studentsIdsByCourseFaculty[] = $student->getId();
                $studentUsersByCourseFaculty[] = $studentUser;
                $coursesByFaculty[] = $course;
                $studentCoursesStatesByCourseFaculty[] = $studentCourseState;

                foreach($transitions as $transition){

                    if($transition->getFromState() == $courseState){

                        $toStates[] = $transition;
                    }
                }
                $coursesTransitionsByCourseFaculty[$studentCourse->getCourse()->getName()] = $toStates;
            }
        }

        return $this->render('Coordinator/internalCoordinatorToDo.html.twig', array(
            'coursesByUserRoleFaculty' => $coursesByUserRoleFaculty,
            'studentsByFaculty' => $studentUsersByFaculty,
            'studentsIdsByFaculty' => $studentsIdsByFaculty,
            'studentCoursesStatesByFaculty' => $studentCoursesStatesByFaculty,
            'coursesTransitionsByFaculty' => $coursesTransitionsByFaculty,
            'coursesByFaculty' => $coursesByFaculty,
            'studentsByCourseFaculty' => $studentUsersByCourseFaculty,
            'studentsIdsByCourseFaculty' => $studentsIdsByCourseFaculty,
            'studentCoursesStatesByCourseFaculty' => $studentCoursesStatesByCourseFaculty,
            'coursesTransitionsByCourseFaculty' => $coursesTransitionsByCourseFaculty));
    }

    /**
     * @Route("/internalCoordinatorList", name="internalCoordinatorList")
     */
    public function internalCoordinatorListAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $internalCoordinatorService = $this->get('internal_coordinator_service');
        $internalCoordinators = $internalCoordinatorService->getAllInternalCoordinatorUsers();

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->addUser($user, $form);
            $internalCoordinatorService = $this->get('internal_coordinator_service');
            $internalCoordinatorService->addUserAsInternalCoordinator($user);

            return $this->redirectToRoute('internalCoordinatorList');
        }

        $facultyShortNames = [];

        foreach($internalCoordinators as $internalCoordinator){

            $facultyShortNames[] = $this->getDoctrine()->getRepository('AppBundle:Faculty')->find($internalCoordinator->getFaculty())->getShortName();
        }

        return $this->render('Coordinator/internalCoordinatorList.html.twig', array('internalCoordinators' => $internalCoordinators,
            'facultyShortNames' => $facultyShortNames, 'form' => $form->createView(), 'formTitle' => 'Add Internal Coordinator'));
    }

    /**
     * @Route("/addInternalCoordinator", name="addInternalCoordinator")
     */
    public function addInternalCoordinatorAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->addUser($user, $form);
            $internalCoordinatorService = $this->get('internal_coordinator_service');
            $internalCoordinatorService->addUserAsInternalCoordinator($user);

            return $this->redirectToRoute('admin');
        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Add Internal Coordinator'));
    }

    /**
     * @Route("/editInternalCoordinator/{id}", name="editInternalCoordinator")
     */
    public function editInternalCoordinatorAction(Request $request, $id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $user = $userService->getUser($id);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->saveUser($user);

            $this->addFlash('notice', 'Internal Coordinator edited');

            return $this->redirectToRoute('internalCoordinatorList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit Internal Coordinator'));
    }

    /**
     * @Route("/deleteInternalCoordinator/{id}", name="deleteInternalCoordinator")
     */
    public function deleteInternalCoordinatorAction($id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $internalCoordinator = $this->getDoctrine()->getRepository('AppBundle:InternalCoordinator')->findOneByUser($id);
        $user = $userService->getUser($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($internalCoordinator);
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('internalCoordinatorList');
    }

    /**
     * @Route("/externalCoordinator", name="externalCoordinator")
     */
    public function externalCoordinatorAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->externalCoordinatorAccess($user);

        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $role = $session->get('role');

        $externalCoordinatorService = $this->get('external_coordinator_service');
        $externalCoordinator = $externalCoordinatorService->getExternalCoordinatorByUserId($user->getId());

        $transitions = $em->getRepository('AppBundle:Transition')->createQueryBuilder('t')
            ->where('t.person = :role')
            ->setParameter('role', $role)
            ->getQuery()->getResult();

        $fromStates = [];
        foreach($transitions as $transition){

            $fromStates[] = $transition->getFromState();
        }

        $subQuery = $em->createQuery('
        SELECT CONCAT(IDENTITY(c2.course),IDENTITY(c2.student),MAX(c2.date))
        FROM AppBundle:CourseStudent c2
        GROUP BY c2.course, c2.student')->getResult();

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('AppBundle:CourseStudent', 'c');

        $query = $em->createNativeQuery('
        SELECT c.*
        FROM course_student c
        WHERE c.state IN (:from_states)
        AND CONCAT(c.course_id, c.student_id, c.date) IN 
	      (:max_date_course_student_ids)', $rsm)
            ->setParameter('from_states', $fromStates)
            ->setParameter('max_date_course_student_ids', $subQuery);
        $studentsCourses = $query->getResult();

        $coursesTransitions = [];
        $studentCoursesStates = [];
        $studentUsers = [];
        $studentsIds = [];
        $courses = [];
        foreach($studentsCourses as $studentCourse){

            $student = $em->getRepository('AppBundle:Student')->find($studentCourse->getStudent());
            $studentUser = $em->getRepository('AppBundle:User')->find($student->getUser());

            $course = $em->getRepository('AppBundle:Course')->find($studentCourse->getCourse());

            $studentCourseState = $studentCourse->getState()->getName();

            if($student->getExternalCoordinator() == $externalCoordinator){

                $studentsIds[] = $student->getId();
                $studentUsers[] = $studentUser;
                $courses[] = $course;
                $studentCoursesStates[] = $studentCourseState;
                $courseState = $studentCourse->getState();
                $toStates = [];
                foreach($transitions as $transition){

                    if($transition->getFromState() == $courseState){

                        $toStates[] = $transition;
                    }
                }

                $coursesTransitions[$studentCourse->getCourse()->getName()] = $toStates;
            }
        }

        return $this->render('Coordinator/externalCoordinatorToDo.html.twig', array('courses' => $courses,
            'students' => $studentUsers, 'studentsIds' => $studentsIds, 'studentCoursesStates' => $studentCoursesStates, 'coursesTransitions' => $coursesTransitions));
    }

    /**
     * @Route("/externalCoordinatorList", name="externalCoordinatorList")
     */
    public function externalCoordinatorListAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorAccess($user);

        $externalCoordinatorService = $this->get('external_coordinator_service');
        $externalCoordinators = $externalCoordinatorService->getAllExternalCoordinatorUsers();

        $userManager = $this->get('fos_user.user_manager');
        $externalCoordinator = new ExternalCoordinator();

        $form = $this->createForm(ExternalCoordinatorType::class, $externalCoordinator);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $em->persist($externalCoordinator);
            $em->flush();
            $userId = $externalCoordinator->getUser();
            $user = $em->getRepository('AppBundle:User')->find($userId);
            $user->setEnabled(1);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('externalCoordinatorList');
        }

        return $this->render('Coordinator/externalCoordinatorList.html.twig', array('externalCoordinators' => $externalCoordinators,
            'form' => $form->createView(), 'formTitle' => 'Add External Coordinator'));
    }

    /**
     * @Route("/addExternalCoordinator", name="addExternalCoordinator")
     */
    public function addExternalCoordinatorAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorAccess($user);

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(ExternalCoordinatorType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->addUser($user, $form);
            $externalCoordinatorService = $this->get('external_coordinator_service');
            $externalCoordinatorService->addUserAsExternalCoordinator($user);

            return $this->redirectToRoute('internalCoordinator');
        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Add External Coordinator'));
    }

    /**
     * @Route("/editExternalCoordinator/{id}", name="editExternalCoordinator")
     */
    public function editExternalCoordinatorAction(Request $request, $id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorAccess($user);

        $externalCoordinatorService = $this->get('external_coordinator_service');
        $externalCoordinator = $externalCoordinatorService->getExternalCoordinatorByUserId($id);

        $form = $this->createForm(ExternalCoordinatorType::class, $externalCoordinator);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $em = $this->getDoctrine()->getManager();
            $userId = $externalCoordinator->getUser();
            $user = $em->getRepository('AppBundle:User')->find($userId);
            $userService->saveUser($user);
            $em->persist($externalCoordinator);
            $em->flush();

            $this->addFlash('notice', 'External Coordinator edited');

            return $this->redirectToRoute('externalCoordinatorList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit External Coordinator'));
    }

    /**
     * @Route("/deleteExternalCoordinator/{id}", name="deleteExternalCoordinator")
     */
    public function deleteExternalCoordinatorAction($id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorAccess($user);

        $externalCoordinator = $this->getDoctrine()->getRepository('AppBundle:ExternalCoordinator')->findOneByUser($id);
        $user = $userService->getUser($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($externalCoordinator);
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('externalCoordinatorList');
    }

    /**
     * @Route("/externalCoordinatorsByUniversity/{id}", name="externalCoordinatorsByUniversity")
     */
    public function externalCoordinatorsByUniversityAction(Request $request, $id)
    {
        $university_id = $request->request->get('university_id');

        $em = $this->getDoctrine()->getManager();
        $externalCoordinators = $em->getRepository('AppBundle:ExternalCoordinator')->findByUniversity($id);

        $query = $em->createQuery(
            'SELECT e.externalCoordinatorId, u.name, u.surname
        FROM AppBundle:ExternalCoordinator e
        JOIN e.user u
        WHERE e.university = :searchPhrase'
        )->setParameter('searchPhrase', $id);

        $externalCoordinators = $query->getArrayResult();

        $response = new Response(json_encode($externalCoordinators));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

}