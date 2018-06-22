<?php

namespace AppBundle\Service;

use AppBundle\Entity\DeansOffice;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class DeansOfficeService extends UserService {

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        parent::__construct($em, $container, $logger);
    }

    public function addUserAsDeansOffice($user){

        $deansOffice = new DeansOffice();
        $deansOffice->setUser($user);
        $this->saveDeansOffice($deansOffice);
    }

    public function saveDeansOffice($deansOffice){

        if($deansOffice){

            $this->em->persist($deansOffice);
            $this->em->flush();
            $this->logger->info('Added new Deans Office user: '.$deansOffice->getId());
        }
    }

    public function getFacultyDeansOfficeUsers($facultyIds){

        $deansOfficeArr = $this->getAllDeansOfficeUsers();

        $repository = $this->em->getRepository('AppBundle:User');

        $query = $repository->createQueryBuilder('u')
            ->where('u.faculty IN (:faculties) AND u.id IN (:deansOffice_ids)')
            ->setParameter('faculties', $facultyIds)
            ->setParameter('deansOffice_ids',$deansOfficeArr)
            ->getQuery();

        return $query->getResult();
    }

    public function getAllDeansOfficeUsers(){

        $deansOffices = $this->getAllDeansOffice();
        $deansOfficeUsers = [];

        foreach ($deansOffices as $deansOffice) {

            $deansOfficeUsers[] = $deansOffice->getUser();
        }

        return $deansOfficeUsers;
    }

    public function getAllDeansOffice(){

        return $this->em->getRepository('AppBundle:DeansOffice')->findAll();
    }

}