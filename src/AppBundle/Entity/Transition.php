<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transition
 *
 * @ORM\Table(name="transition")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TransitionRepository")
 */
class Transition
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="from_state", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $fromState;

    /**
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="to_state", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $toState;

    /**
     * @var string
     *
     * @ORM\Column(name="person", type="string", length=255)
     */
    private $person;

    /**
     * @var string
     *
     * @ORM\Column(name="buttonString", type="string", length=255)
     */
    private $buttonString;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=255)
     */
    private $color;

    /**
     * @var bool
     *
     * @ORM\Column(name="special", type="boolean")
     */
    private $special;


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
     * Set fromState
     *
     * @param integer $fromState
     *
     * @return Transition
     */
    public function setFromState($fromState)
    {
        $this->fromState = $fromState;

        return $this;
    }

    /**
     * Get fromState
     *
     * @return int
     */
    public function getFromState()
    {
        return $this->fromState;
    }

    /**
     * Set toState
     *
     * @param integer $toState
     *
     * @return Transition
     */
    public function setToState($toState)
    {
        $this->toState = $toState;

        return $this;
    }

    /**
     * Get toState
     *
     * @return int
     */
    public function getToState()
    {
        return $this->toState;
    }

    /**
     * Set person
     *
     * @param string $person
     *
     * @return Transition
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return string
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set buttonString
     *
     * @param string $buttonString
     *
     * @return Transition
     */
    public function setButtonString($buttonString)
    {
        $this->buttonString = $buttonString;

        return $this;
    }

    /**
     * Get buttonString
     *
     * @return string
     */
    public function getButtonString()
    {
        return $this->buttonString;
    }

    /**
     * Set color
     *
     * @param string $color
     *
     * @return Transition
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set special
     *
     * @param boolean $special
     *
     * @return Transition
     */
    public function setSpecial($special)
    {
        $this->special = $special;

        return $this;
    }

    /**
     * Get special
     *
     * @return bool
     */
    public function getSpecial()
    {
        return $this->special;
    }
}

