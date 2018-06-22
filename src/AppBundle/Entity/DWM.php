<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DWM
 *
 * @ORM\Table(name="d_w_m")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DWMRepository")
 */
class DWM
{
    /**
     * @var int
     *
     * @ORM\Column(name="dwm_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $dwm_id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)
     */
    private $user;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->dwm_id;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return DWM
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

