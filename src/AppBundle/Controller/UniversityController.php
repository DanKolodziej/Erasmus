<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Country;
use AppBundle\Entity\University;
use AppBundle\Entity\faculty;
use AppBundle\Form\CountryType;
use AppBundle\Form\UniversityType;
use AppBundle\Form\FacultyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UniversityController extends Controller{

    /**
     * @Route("/universityList", name="universityList")
     */
    public function universityListAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorOrInternalCoordinatorAccess($user);

        $universityService = $this->get('university_service');
        $universities = $universityService->getAllUniversities();

        $university = new University();

        $form = $this->createForm(UniversityType::class, $university);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $universityService = $this->get('university_service');
            $universityService->saveUniversity($university);

            return $this->redirectToRoute('universityList');
        }

        return $this->render('Administrator/universityList.html.twig', array('universities' => $universities, 'form' => $form->createView(), 'formTitle' => 'Add University'));
    }

    /**
     * @Route("addUniversity", name="addUniversity")
     */
    public function addUniversityAction(Request $request)
    {
        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorOrInternalCoordinatorAccess($user);

        $university = new University();

        $form = $this->createForm(UniversityType::class, $university);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $universityService = $this->get('university_service');
            $universityService->saveUniversity($university);

            return $this->redirectToRoute('admin');
        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Add University'));
    }

    /**
     * @Route("/editUniversity/{id}", name="editUniversity")
     */
    public function editUniversityAction(Request $request, $id){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorOrInternalCoordinatorAccess($user);

        $universityService = $this->get('university_service');
        $university = $universityService->getUniversity($id);

        $form = $this->createForm(UniversityType::class, $university);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $universityService->saveUniversity($university);

            $this->addFlash('notice', 'University edited');

            return $this->redirectToRoute('universityList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit University'));
    }

    /**
     * @Route("/deleteUniversity/{id}", name="deleteUniversity")
     */
    public function deleteUniversityAction(Request $request, $id){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorOrInternalCoordinatorAccess($user);

        $universityService = $this->get('university_service');
        try {

            $universityService->deleteUniversity($id);
        } catch(\Doctrine\DBAL\DBALException $e) {

            $this->addFlash('error', 'Can not currently delete this university. 
            There are existing users assigned to it');
        }

        return $this->redirectToRoute('universityList');
    }
}