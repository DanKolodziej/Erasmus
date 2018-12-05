<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserNoFacultyExtensionType;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class DWMController extends Controller {

    /**
     * @Route("/dwmList", name="dwmList")
     */
    public function dwmListAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $facultyId = $user->getFaculty();
        $dwmService = $this->get('dwm_service');
        $dwms = $dwmService->getAllDWMUsers($facultyId);

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(UserNoFacultyExtensionType::class, $user);
        $form->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 20px')));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->addUser($user, $form);
            $dwmService = $this->get('dwm_service');
            $dwmService->addUserAsDWM($user);

            return $this->redirectToRoute('dwmList');
        }

        return $this->render('DWM/DWMList.html.twig', array('dwms' => $dwms, 'form' => $form->createView(), 'formTitle' => 'Add DWM'));
    }

    /**
     * @Route("/addDWM", name="addDWM")
     */
    public function addDWMAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->createUser();

        $form = $this->createForm(UserNoFacultyExtensionType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->addUser($user, $form);
            $dwmService = $this->get('dwm_service');
            $dwmService->addUserAsDWM($user);

            return $this->redirectToRoute('admin');
        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Add DWM'));
    }

    /**
     * @Route("/editDWM/{id}", name="editDWM")
     */
    public function editDWMAction(Request $request, $id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $user = $userService->getUser($id);

        $form = $this->createForm(UserNoFacultyExtensionType::class, $user);
        $form->add('save', SubmitType::class, array('label' => 'Save', 'attr' => array('class' => 'btn btn-primary', 'style' => 'margin-bottom:15px; margin-left: 20px')));

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $userService->saveUser($user);

            $this->addFlash('notice', 'DWM worker edited');

            return $this->redirectToRoute('dwmList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit DWM Worker'));
    }

    /**
     * @Route("/deleteDWM/{id}", name="deleteDWM")
     */
    public function deleteDWMAction($id)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $dwm = $this->getDoctrine()->getRepository('AppBundle:DWM')->findOneByUser($id);
        $user = $userService->getUser($id);

        try {

            $em = $this->getDoctrine()->getManager();
            $em->remove($dwm);
            $em->remove($user);
            $em->flush();
        } catch(\Doctrine\DBAL\DBALException $e) {

            $this->addFlash('error', 'Can not currently delete this dwm worker.');
        }

//        $em = $this->getDoctrine()->getManager();
//        $em->remove($dwm);
//        $em->remove($user);
//        $em->flush();

        return $this->redirectToRoute('dwmList');
    }
}