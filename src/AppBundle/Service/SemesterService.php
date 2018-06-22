<?php

namespace AppBundle\Service;

use AppBundle\Entity\Semester;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;
use Psr\Log\LoggerInterface;

class SemesterService {

    private $em;
    private $container;
    private $logger;

    public function __construct(EntityManager $em, Container $container, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->container = $container;
        $this->logger = $logger;
    }

    public function saveSemester($semester){

        $this->em->persist($semester);
        $this->em->flush();

        $this->logger->info('New Semester: '.$semester->getSeason().' '.$semester->getYear().' '.$semester->getStatus());
    }

    public function getSemester($semesterId){

        return $this->em->getRepository('AppBundle:Semester')->find($semesterId);
    }

    public function shiftSemesters(){

        $previousSemester = $this->getSemesterByStatus('Previous semester');
        $currentSemester = $this->getSemesterByStatus('Current semester');
        $nextSemester = $this->getSemesterByStatus('Next semester');

        $previousSemester->setYear($currentSemester->getYear());
        $previousSemester->setSeason($currentSemester->getSeason());

        $currentSemester->setYear($nextSemester->getYear());
        $currentSemester->setSeason($nextSemester->getSeason());

        if($nextSemester->getSeason() == 'Summer'){

            $nextYear = $nextSemester->getYear();
            $nextYearArr = explode('/', $nextYear);
            settype($nextYearArr[0], 'integer');
            settype($nextYearArr[1], 'integer');
            $nextYearArr[0]++;
            $nextYearArr[1]++;
            $newYear = implode('/', $nextYearArr);

            $nextSemester->setYear($newYear);
            $nextSemester->setSeason('Winter');
        } else {

            $nextSemester->setSeason('Summer');
        }

        $this->saveSemester($previousSemester);
        $this->saveSemester($currentSemester);
        $this->saveSemester($nextSemester);

        $this->shiftSemesterCourses($previousSemester, $currentSemester, $nextSemester);
    }

    public function shiftSemesterCourses($previousSemester, $currentSemester, $nextSemester){

        $previousSemesterCourses = $previousSemester->getCoursesCollection();
        $currentSemesterCourses = $currentSemester->getCoursesCollection();
        $nextSemesterCourses = $nextSemester->getCoursesCollection();

        $previousSemester->setCoursesCollection($currentSemesterCourses);
        $currentSemester->setCoursesCollection($nextSemesterCourses);
        $nextSemester->setCoursesCollection($previousSemesterCourses);

        $this->saveSemester($previousSemester);
        $this->saveSemester($currentSemester);
        $this->saveSemester($nextSemester);
    }

    public function getSemesterByStatus($status){

        return $this->em->getRepository('AppBundle:Semester')->findOneByStatus($status);
    }

    public function getAllSemesters(){

        return $this->em->getRepository('AppBundle:Semester')->findAll();
    }

}