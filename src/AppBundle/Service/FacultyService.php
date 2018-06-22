<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class FacultyService
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

    public function getFaculty($id){

        return $this->em->getRepository('AppBundle:Faculty')->find($id);
    }

    public function saveFaculty($faculty){

        if($faculty){

            $this->em->persist($faculty);
            $this->em->flush();
            $this->logger->info('Added/Edited Faculty: '.$faculty->getName());
        }
    }

    public function getAllMainFaculties(){

        $faculties = $this->getAllFaculties();

        $mainFaculties = [];
        foreach($faculties as $faculty){

            $upperFacultyId = $faculty->getUpperFaculty();
            if(!isset($upperFacultyId)){

                $mainFaculties[] = $faculty;
            }
        }

        return $mainFaculties;
    }

    public function getAllFaculties(){

        return $this->em->getRepository('AppBundle:Faculty')->findAll();
    }

    public function getFacultyName($facultyId){

        return $this->em->getRepository("AppBundle:Faculty")->find($facultyId)->getName();
    }

    public function getFacultyShortName($facultyId){

        return $this->em->getRepository("AppBundle:Faculty")->find($facultyId)->getShortName();
    }

    public function deleteFaculty($id){

        $faculty = $this->getFaculty($id);
        $this->deleteFacultyFromDB($faculty);
    }

    public function deleteFacultyFromDB($faculty){

        if($faculty){

            $this->em->remove($faculty);
            $this->em->flush();
            $this->logger->info('Deleted Faculty: '.$faculty->getName());
        }
    }

    public function getUpperFaculty($faculty){

        $upperFacultyId = $faculty->getUpperFaculty();
        if(isset($upperFacultyId)){
            return $this->em->getRepository('AppBundle:Faculty')->find($upperFacultyId);
        }
        return null;
    }
}