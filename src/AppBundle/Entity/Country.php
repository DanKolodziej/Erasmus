<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CountryRepository")
 */
class Country
{
    /**
     * @var int
     *
     * @ORM\Column(name="country_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $countryId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="A name is required for this country")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=10, unique=true)
     *
     * @Assert\NotBlank(message="A code is required for this country")
     * @Assert\Length(max=10, maxMessage="Country code can have only 10 characters")
     */
    private $code;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->countryId;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Country
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
     * @return Country
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

    public function __toString()
    {
        return $this->name;
    }
}

