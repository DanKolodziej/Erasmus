<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('Basic/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/user", name="userLogin")
     */
    public function userLoginAction(Request $request)
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');

        $session = $request->getSession();
        $session->start();

        if($userService->isAdmin($user)){

            $session->set('role', 'Administrator');
            return $this->redirectToRoute('admin');
        }

        if($userService->isInternalCoordinator($user)){

            $session->set('role', 'Internal Coordinator');
            return $this->redirectToRoute('internalCoordinator');
        }

        if($userService->isExternalCoordinator($user)){

            $session->set('role', 'External Coordinator');
            return $this->redirectToRoute('externalCoordinator');
        }

        if($userService->isDeansOffice($user)){

            $session->set('role', 'Deans Office');
            return $this->redirectToRoute('deansOffice');
        }

        if($userService->isStudent($user)){

            $session->set('role', 'Student');
            return $this->redirectToRoute('enrollmentsState');
        }

        if($userService->isDWM($user)){

            $session->set('role', 'DWM');
            return $this->redirectToRoute('dwmStudentList');
        }

        return $this->redirectToRoute('enrollments');
    }

    /**
     * @Route("/list", name="list")
     */
    public function listAction(Request $request)
    {

        $referer = $request->headers->get('referer');
        $router = $this->get('router');
        $route = $router->match($referer);
        return $this->redirectToRoute($route);
    }

    /**
     * @Route("/panel", name="panel")
     */
    public function panel()
    {

        $user = $this->getUser();
        $userService = $this->get('user_service');

        if($userService->isAdmin($user)){

            return $this->redirectToRoute('admin');
        }

        if($userService->isInternalCoordinator($user)){

            return $this->redirectToRoute('internalCoordinator');
        }

        if($userService->isDeansOffice($user)){
           
            return $this->redirectToRoute('deansOffice');
        }

        if($userService->isStudent($user)){


            return $this->redirectToRoute('student');
        }

        if($userService->isExternalCoordinator($user)){


            return $this->redirectToRoute('externalCoordinator');
        }

        if($userService->isDWM($user)){


            return $this->redirectToRoute('dwm');
        }

        return $this->redirectToRoute('enrollments');
    }
}