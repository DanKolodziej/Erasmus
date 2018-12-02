<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Student
 *
 * @ORM\Table(name="student")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StudentRepository")
 * @UniqueEntity(
 *     fields={"indexNumber"},
 *     message="There already is a student with this index number"
 * )
 */
class Student
{
    /**
     * @var int
     *
     * @ORM\Column(name="student_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $student_id;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="index_number", type="integer", nullable=true)
     *
     * @Assert\GreaterThan(value=0, message="Index number can not be a negative number")
     */
    private $indexNumber;

    /**
     * @ORM\ManyToOne(targetEntity="University")
     * @ORM\JoinColumn(name="university_id", referencedColumnName="university_id")
     */
    private $university;

    /**
     * @ORM\ManyToOne(targetEntity="ExternalCoordinator")
     * @ORM\JoinColumn(name="external_coordinator_id", referencedColumnName="external_coordinator_id")
     */
    private $externalCoordinator;

    /**
     * @ORM\OneToMany(targetEntity="CourseStudent", mappedBy="student")
     */
    private $courseStudents;

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseStudents = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->student_id;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return student
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
     * Set indexNumber
     *
     * @param integer $indexNumber
     *
     * @return student
     */
    public function setIndexNumber($indexNumber)
    {
        $this->indexNumber = $indexNumber;

        return $this;
    }

    /**
     * Get indexNumber
     *
     * @return int
     */
    public function getIndexNumber()
    {
        return $this->indexNumber;
    }

    /**
     * Set university
     *
     * @param integer $university
     *
     * @return student
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

    /**
     * Set externalCoordinator
     *
     * @param integer $externalCoordinator
     *
     * @return student
     */
    public function setExternalCoordinator($externalCoordinator)
    {
        $this->externalCoordinator = $externalCoordinator;

        return $this;
    }

    /**
     * Get externalCoordinator
     *
     * @return int
     */
    public function getExternalCoordinator()
    {
        return $this->externalCoordinator;
    }

    /**
     * @param CourseStudent $courseStudent
     */
    public function addCourseStudent(CourseStudent $courseStudent)
    {
        if ($this->courseStudents->contains($courseStudent)) {
            return;
        }
        $this->courseStudents->add($courseStudent);
        $courseStudent->setStudent($this->student_id);
    }
    /**
     * @param CourseStudent $courseStudent
     */
    public function removeCourseStudent(CourseStudent $courseStudent)
    {
        if (!$this->courseStudents->contains($courseStudent)) {
            return;
        }
        $this->courseStudents->removeElement($courseStudent);
        $courseStudent->setStudent(null);
    }

    /**
     * @return array
     */
    public function getCourseStudents() {
        $courseStudentArr = [];
        foreach ($this->courseStudents as $courseStudent) {
            $courseStudentArr[] = $courseStudent;
        }
        return $courseStudentArr;
    }

    /**
     * Get courseStudents as ArrayCollection.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCourseStudentsCollection() {
        return $this->courseStudents;
    }

    /**
     * Set courseStudents.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $courseStudents
     * @return Student
     */
    public function setCourseStudentsCollection($courseStudents) {
        $this->courseStudents = $courseStudents;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}

