<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InternalCoordinator
 *
 * @ORM\Table(name="internal_coordinator")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\internal_coordinatorRepository")
 */
class InternalCoordinator
{
    /**
     * @var int
     *
     * @ORM\Column(name="internal_coordinator_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $internal_coordinator_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)
     */
    private $user;


    /**
     * Get internal_coordinator_id
     *
     * @return int
     */
    public function getId()
    {
        return $this->internal_coordinator_id;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return InternalCoordinator
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
}

