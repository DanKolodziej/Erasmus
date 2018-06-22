<?php

namespace AppBundle\Service;

use AppBundle\Entity\ExternalCoordinator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class ExternalCoordinatorService extends UserService {

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        parent::__construct($em, $container, $logger);
    }

    public function addUserAsExternalCoordinator($user){

        $externalCoordinator = new ExternalCoordinator();
        $externalCoordinator->setUser($user);
        $this->saveExternalCoordinator($externalCoordinator);
    }

    public function saveExternalCoordinator($externalCoordinator){

        if($externalCoordinator){

            $this->em->persist($externalCoordinator);
            $this->em->flush();
            $this->logger->info('Added new External Coordinator user: '.$externalCoordinator->getId());
        }
    }

    public function getFacultyExternalCoordinatorUsers($facultyIds){

        $externalCoordinatorArr = $this->getAllExternalCoordinatorUsers();

        $repository = $this->em->getRepository('AppBundle:User');

        $query = $repository->createQueryBuilder('u')
            ->where('u.faculty IN (:faculties) AND u.id IN (:external_coordinator_ids)')
            ->setParameter('faculties', $facultyIds)
            ->setParameter('external_coordinator_ids',$externalCoordinatorArr)
            ->getQuery();

        return $query->getResult();
    }

    public function getAllExternalCoordinatorUsers(){

        $externalCoordinators = $this->getAllExternalCoordinators();
        $externalCoordinatorUsers = [];

        foreach ($externalCoordinators as $externalCoordinator) {

            $externalCoordinatorUsers[] = $externalCoordinator->getUser();
        }

        return $externalCoordinatorUsers;
    }

    public function getAllExternalCoordinators(){

        return $this->em->getRepository('AppBundle:ExternalCoordinator')->findAll();
    }

    public function getExternalCoordinatorByUserId($userId){

        return $this->em->getRepository('AppBundle:ExternalCoordinator')->findOneByUser($userId);
    }

}