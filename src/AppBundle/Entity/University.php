<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * University
 *
 * @ORM\Table(name="university")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UniversitiesRepository")
 * @UniqueEntity(
 *     fields={"name"},
 *     message="There already is a university with this name"
 * )
 */
class University
{
    /**
     * @var int
     *
     * @ORM\Column(name="university_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $universityId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="A name is required for this university")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10)
     *
     * @Assert\NotBlank(message="A code is required for this university")
     * @Assert\Length(max=10, maxMessage="University code can have only 10 characters")
     */
    private $code;

    /**
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="country_id")
     */
    private $country;


    /**
     * Get universityId
     *
     * @return int
     */
    public function getId()
    {
        return $this->universityId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return University
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
     * Set code
     *
     * @param string $code
     *
     * @return University
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set country
     *
     * @param string $country
     *
     * @return University
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
}

