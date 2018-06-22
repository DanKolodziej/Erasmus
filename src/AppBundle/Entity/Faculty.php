<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Faculty
 *
 * @ORM\Table(name="faculty")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\facultyRepository")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="There already is a faculty with this name"
 * )
 * @UniqueEntity(
 *     fields={"shortName"},
 *     message="There already is a faculty with this short name"
 * )
 */
class Faculty
{
    /**
     * @var int
     *
     * @ORM\Column(name="faculty_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $facultyId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="A name is required for this faculty")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=10, unique=true)
     *
     * @Assert\NotBlank(message="A short name is required for this faculty")
     */
    private $shortName;

    /**
     * @ORM\ManyToOne(targetEntity="faculty")
     * @ORM\JoinColumn(name="upper_faculty_id", referencedColumnName="faculty_id", nullable=true)
     */
    private $upperFaculty;


    /**
     * Get faculty_id
     *
     * @return int
     */
    public function getId()
    {
        return $this->facultyId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Faculty
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
     * Set shortName
     *
     * @param string $shortName
     *
     * @return Faculty
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set upperFaculty
     *
     * @param integer $upperFaculty
     *
     * @return Faculty
     */
    public function setUpperFaculty($upperFaculty)
    {
        $this->upperFaculty = $upperFaculty;

        return $this;
    }

    /**
     * Get upperFaculty
     *
     * @return int
     */
    public function getUpperFaculty()
    {
        return $this->upperFaculty;
    }

    public function __toString()
    {
        return $this->name.' ('.$this->shortName.')';
    }
}

