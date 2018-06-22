<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\userRepository")
 * @ORM\Entity
 *
 * @UniqueEntity(
 *     fields={"username"},
 *     message="There already is a user with this username"
 * )
 * @UniqueEntity(
 *     fields={"email"},
 *     message="There already is a user with this email"
 * )
 */
class User extends BaseUser
{

    public function __construct() {
        parent::__construct();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank(message="A name is required for this user")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="surname", type="string", length=255)
     *
     * @Assert\NotBlank(message="A surname is required for this user")
     */
    private $surname;

    /**
     * @ORM\ManyToOne(targetEntity="Faculty")
     * @ORM\JoinColumn(name="faculty_id", referencedColumnName="faculty_id", nullable=true)
     */
    private $faculty;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string $surname
     *
     * @return User
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set faculty
     *
     * @param integer $faculty
     *
     * @return User
     */
    public function setFaculty($faculty)
    {
        $this->faculty = $faculty;

        return $this;
    }

    /**
     * Get faculty
     *
     * @return int
     */
    public function getFaculty()
    {
        return $this->faculty;
    }
}

