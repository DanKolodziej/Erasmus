<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DeansOffice
 *
 * @ORM\Table(name="deans_office")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\deans_officeRepository")
 */
class DeansOffice
{
    /**
     * @var int
     *
     * @ORM\Column(name="deans_office_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $deans_office_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)
     */
    private $user;


    /**
     * Get deans_office_id
     *
     * @return int
     */
    public function getId()
    {
        return $this->deans_office_id;
    }


    /**
     * Set user
     *
     * @param integer $user
     *
     * @return DeansOffice
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

