<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class CountryService
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

    public function saveCountry($country){

        if($country){

            $this->em->persist($country);
            $this->em->flush();
            $this->logger->info('Added/Edited Country: '.$country->getName());
        }
    }

    public function deleteCountry($countryId){

        $country = $this->getCountry($countryId);
        $this->deleteCountryFromDB($country);
    }

    public function deleteCountryFromDB($country){

        if($country){

            $this->em->remove($country);
            $this->em->flush();
            $this->logger->info('Deleted Country: '.$country->getName());
        }
    }

    public function getCountry($countryId){

        return $this->em->getRepository('AppBundle:Country')->find($countryId);
    }

    public function getAllCountries(){

        return $this->em->getRepository('AppBundle:Country')->findAll();
    }
}