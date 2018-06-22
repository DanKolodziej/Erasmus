<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExternalCoordinator
 *
 * @ORM\Table(name="external_coordinator")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\external_coordinatorRepository")
 */
class ExternalCoordinator
{
    /**
     * @var int
     *
     * @ORM\Column(name="external_coordinator_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $externalCoordinatorId;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="University")
     * @ORM\JoinColumn(name="university_id", referencedColumnName="university_id")
     */
    private $university;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->externalCoordinatorId;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return ExternalCoordinator
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set university
     *
     * @param integer $university
     *
     * @return ExternalCoordinator
     */
    public function setUniversity($university)
    {
        $this->university = $university;

        return $this;
    }

    /**
     * Get university
     *
     * @return int
     */
    public function getUniversity()
    {
        return $this->university;
    }
}

