<?php

namespace AppBundle\Service;

use AppBundle\Entity\DWM;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class DWMService extends UserService {

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        parent::__construct($em, $container, $logger);
    }

    public function addUserAsDWM($user){

        $dwm = new DWM();
        $dwm->setUser($user);
        $this->saveDWM($dwm);
    }

    public function saveDWM($dwm){

        if($dwm){

            $this->em->persist($dwm);
            $this->em->flush();
            $this->logger->info('Added new DWM user: '.$dwm->getId());
        }
    }

    public function getAllDWMUsers(){

        $dwms = $this->getAllDWMs();
        $dwmUsers = [];

        foreach ($dwms as $dwm) {

            $dwmUsers[] = $dwm->getUser();
        }

        return $dwmUsers;
    }

    public function getAllDWMs(){

        return $this->em->getRepository('AppBundle:DWM')->findAll();
    }

}