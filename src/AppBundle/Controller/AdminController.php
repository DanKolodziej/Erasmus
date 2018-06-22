<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Country;
use AppBundle\Entity\University;
use AppBundle\Entity\faculty;
use AppBundle\Form\CountryType;
use AppBundle\Form\SemesterType;
use AppBundle\Form\UniversityType;
use AppBundle\Form\FacultyType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends Controller {

    /**
     * @Route("/admin", name="admin")
     */
    public function adminPanelAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        return $this->render('Administrator/adminPanel.html.twig');
    }

    /**
     *@Route("/facultyList", name="facultyList")
     */
    public function facultyListAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $facultyService = $this->get('faculty_service');
        $faculties = $facultyService->getAllFaculties();

        $faculty = new faculty();

        $form = $this->createForm(FacultyType::class, $faculty);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $facultyService = $this->get('faculty_service');
            $facultyService->saveFaculty($faculty);

            return $this->redirectToRoute('facultyList');
        }

        return $this->render('Administrator/facultyList.html.twig', array('faculties' => $faculties, 'form' => $form->createView(), 'formTitle' => 'Add Faculty'));
    }

    /**
     *@Route("addFaculty", name="addFaculty")
     */
    public function addFacultyAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $faculty = new faculty();

        $form = $this->createForm(FacultyType::class, $faculty);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $facultyService = $this->get('faculty_service');
            $facultyService->saveFaculty($faculty);

            return $this->redirectToRoute('admin');
        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Add Faculty'));
    }

    /**
     * @Route("/editFaculty/{id}", name="editFaculty")
     */
    public function editFacultyAction(Request $request, $id){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $facultyService = $this->get('faculty_service');
        $faculty = $facultyService->getFaculty($id);

        $form = $this->createForm(FacultyType::class, $faculty);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $facultyService->saveFaculty($faculty);

            $this->addFlash('notice', 'Faculty edited');

            return $this->redirectToRoute('facultyList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit Faculty'));
    }

    /**
     * @Route("/deleteFaculty/{id}", name="deleteFaculty")
     */
    public function deleteFacultyAction(Request $request, $id){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $facultyService = $this->get('faculty_service');
        try {

            $facultyService->deleteFaculty($id);
        } catch(\Doctrine\DBAL\DBALException $e) {

            $this->addFlash('error', 'Can not currently delete this faculty. 
            There are existing users/courses assigned to it');
        }

        return $this->redirectToRoute('facultyList');
    }



    /**
     * @Route("/countryList", name="countryList")
     */
    public function countryListAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $countryService = $this->get('country_service');
        $countries = $countryService->getAllCountries();

        $country = new Country();

        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $countryService = $this->get('country_service');
            $countryService->saveCountry($country);

            return $this->redirectToRoute('countryList');
        }

        return $this->render('Administrator/countryList.html.twig', array('countries' => $countries, 'form' => $form->createView(), 'formTitle' => 'Add Country'));
    }

    /**
     * @Route("/editCountry/{id}", name="editCountry")
     */
    public function editCountryAction(Request $request, $id){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $countryService = $this->get('country_service');
        $country = $countryService->getCountry($id);

        $form = $this->createForm(CountryType::class, $country);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $countryService->saveCountry($country);

            $this->addFlash('notice', 'Country edited');

            return $this->redirectToRoute('countryList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit Country'));
    }

    /**
     * @Route("/deleteCountry/{id}", name="deleteCountry")
     */
    public function deleteCountryAction(Request $request, $id){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $countryService = $this->get('country_service');
        try {

            $countryService->deleteCountry($id);
        } catch(\Doctrine\DBAL\DBALException $e) {

            $this->addFlash('error', 'Can not currently delete this country. 
            There are existing users/universities assigned to it');
        }

        return $this->redirectToRoute('countryList');
    }

    /**
     * @Route("/semesterList", name="semesterList")
     */
    public function semesterListAction(Request $request){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $semesterService = $this->get('semester_service');
        $semesters = $semesterService->getAllSemesters();

        return $this->render('Administrator/semesterList.html.twig', array('semesters' => $semesters));
    }

    /**
     * @Route("/editSemester/{id}", name="editSemester")
     */
    public function editSemesterAction(Request $request, $id){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $semesterService = $this->get('semester_service');
        $semester = $semesterService->getSemester($id);

        $form = $this->createForm(SemesterType::class, $semester);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $semesterService->saveSemester($semester);

            $this->addFlash('notice', 'Semester edited');

            return $this->redirectToRoute('semesterList');

        }

        return $this->render('Basic/addOrEditForm.html.twig', array('form' => $form->createView(), 'formTitle' => 'Edit Semester'));
    }

    /**
     * @Route("/semesterList/shiftSemesters", name="shiftSemesters")
     */
    public function shiftSemestersAction(){

        $user = $this->getUser();
        $userService = $this->get('user_service');
        $userService->administratorAccess($user);

        $semesterService = $this->get('semester_service');
        $semesterService->shiftSemesters();
        $semesters = $semesterService->getAllSemesters();

        return $this->redirectToRoute('semesterList', array('semesters' => $semesters));
    }
}