<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DeansOfficeController extends Controller {

    /**
     * @Route("/deansOffice", name="deansOffice")
     */
    public function deansOfficeAction(Request $request)
    {
        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->deansOfficeAccess($user);

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

            if(in_array($studentUser->getFaculty(), $facultyIds)){

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

        return $this->render('DeansOffice/deansOfficeToDo.html.twig', array('courses' => $courses,
            'students' => $studentUsers, 'studentsIds' => $studentsIds, 'studentCoursesStates' => $studentCoursesStates, 'coursesTransitions' => $coursesTransitions));
    }

    /**
     * @Route("/deansOfficeList", name="deansOfficeList")
     */
    public function deansOfficeListAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorAccess($user);

        $facultyId = $user->getFaculty();
        $facultyService = $this->get('faculty_service');
        $faculty = $facultyService->getFaculty($facultyId);
        $upperFacultyId = $faculty->getUpperFaculty();

        $facultyIds = array($facultyId);
        if(isset($upperFacultyId)){

            $facultyIds[] = $upperFacultyId;
        }

        $deansOfficeService = $this->get('deans_office_service');
        $deansOffices = $deansOfficeService->getFacultyDeansOfficeUsers($facultyIds);

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setFaculty($facultyId);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->addUser($user, $form);
            $deansOfficeService = $this->get('deans_office_service');
            $deansOfficeService->addUserAsDeansOffice($user);

            return $this->redirectToRoute('deansOfficeList');
        }

        return $this->render('DeansOffice/deansOfficeList.html.twig', array('deansOffices' => $deansOffices,
            'form' => $form->createView(), 'formTitle' => 'Add Deans Office Worker'));
    }

    /**
     * @Route("/addDeansOffice", name="addDeansOffice")
     */
    public function addDeansOfficeAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->InternalCoordinatorAccess($user);

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->addUser($user, $form);
            $deansOfficeService = $this->get('deans_office_service');
            $deansOfficeService->addUserAsDeansOffice($user);

            return $this->redirectToRoute('internalCoordinator');
        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Add Deans Office'));
    }

    /**
     * @Route("/editDeansOffice/{id}", name="editDeansOffice")
     */
    public function editDeansOfficeAction(Request $request, $id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorAccess($user);

        $user = $userService->getUser($id);

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->saveUser($user);

            $this->addFlash('notice', 'Deans Office Worker edited');

            return $this->redirectToRoute('deansOfficeList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit Deans Office'));
    }

    /**
     * @Route("/deleteDeansOffice/{id}", name="deleteDeansOffice")
     */
    public function deleteDeansOfficeAction($id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->internalCoordinatorAccess($user);

        $deansOffice = $this->getDoctrine()->getRepository('AppBundle:DeansOffice')->findOneByUser($id);
        $user = $userService->getUser($id);

        try {

            $em = $this->getDoctrine()->getManager();
            $em->remove($deansOffice);
            $em->remove($user);
            $em->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {

            $this->addFlash('error', 'Can not currently delete this deans office worker. 
            There are students with courses assigned to him/her');
        }

//        $em = $this->getDoctrine()->getManager();
//        $em->remove($deansOffice);
//        $em->remove($user);
//        $em->flush();

        return $this->redirectToRoute('deansOfficeList');
    }
}