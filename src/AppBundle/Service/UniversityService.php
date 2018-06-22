<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class UniversityService
{
    private $em;
    private $container;
    private $logger;

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function saveUniversity($university){

        if($university){

            $this->em->persist($university);
            $this->em->flush();
            $this->logger->info('Added/Edited University: '.$university->getName());
        }
    }

    public function deleteUniversity($universityId){

        $university = $this->getUniversity($universityId);
        $this->deleteUniversityFromDB($university);
    }

    public function deleteUniversityFromDB($university){

        if($university){

            $this->em->remove($university);
            $this->em->flush();
            $this->logger->info('Deleted University: '.$university->getName());
        }
    }

    public function getUniversity($universityId){

        return $this->em->getRepository('AppBundle:University')->find($universityId);
    }

    public function getAllUniversities(){

        return $this->em->getRepository('AppBundle:University')->findAll();
    }

}