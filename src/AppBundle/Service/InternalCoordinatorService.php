<?php

namespace AppBundle\Service;

use AppBundle\Entity\InternalCoordinator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class InternalCoordinatorService extends UserService {

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        parent::__construct($em, $container, $logger);
    }

    public function addUserAsInternalCoordinator($user){

        $internalCoordinator = new InternalCoordinator();
        $internalCoordinator->setUser($user);
        $this->saveInternalCoordinator($internalCoordinator);
    }

    public function saveInternalCoordinator($internalCoordinator){

        if($internalCoordinator){

            $this->em->persist($internalCoordinator);
            $this->em->flush();
            $this->logger->info('Added new Internal Coordinator user: '.$internalCoordinator->getId());
        }
    }

    public function getFacultyInternalCoordinatorUsers($facultyId){

        $internalCoordinatorArr = $this->getAllInternalCoordinatorUsers();

        $repository = $this->em->getRepository('AppBundle:User');

        $query = $repository->createQueryBuilder('u')
            ->where('u.faculty = :faculty AND u.id IN (:internalCoordinator_ids)')
            ->setParameter('faculty', $facultyId)
            ->setParameter('internalCoordinator_ids',$internalCoordinatorArr)
            ->getQuery();

        return $query->getResult();
    }

    public function getAllInternalCoordinatorUsers(){

        $internalCoordinators = $this->getAllInternalCoordinators();
        $internalCoordinatorUsers = [];

        foreach ($internalCoordinators as $internalCoordinator) {

            $internalCoordinatorUsers[] = $internalCoordinator->getUser();
        }

        return $internalCoordinatorUsers;
    }

    public function getAllInternalCoordinators(){

        return $this->em->getRepository('AppBundle:InternalCoordinator')->findAll();
    }

}