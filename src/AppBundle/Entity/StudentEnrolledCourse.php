<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * StudentEnrolledCourse
 *
 * @ORM\Table(name="student_enrolled_course")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StudentEnrolledCourseRepository")
 * @UniqueEntity(
 *     fields={"course, student"},
 *     message="Student is already signed for this course"
 * )
 */
class StudentEnrolledCourse
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $studentEnrolledCourseId;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Course")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="course_id")
     */
    private $course;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="student_id")
     */
    private $student;

    /**
     * @var bool
     *
     * @ORM\Column(name="internal_coordinator_acceptance", type="boolean", nullable=true)
     */
    private $internalCoordinatorAcceptance;

    /**
     * @var bool
     *
     * @ORM\Column(name="external_coordinator_acceptance", type="boolean", nullable=true)
     */
    private $externalCoordinatorAcceptance;

    /**
     * @var bool
     *
     * @ORM\Column(name="deans_office_acceptance", type="string", length=255)
     */
    private $deansOfficeAcceptance;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->studentEnrolledCourseId;
    }

    /**
     * Set course
     *
     * @param integer $course
     *
     * @return StudentEnrolledCourse
     */
    public function setCourse($course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return int
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set student
     *
     * @param integer $student
     *
     * @return StudentEnrolledCourse
     */
    public function setStudent($student)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return int
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set internalCoordinatorAcceptance
     *
     * @param boolean $internalCoordinatorAcceptance
     *
     * @return StudentEnrolledCourse
     */
    public function setInternalCoordinatorAcceptance($internalCoordinatorAcceptance)
    {
        $this->internalCoordinatorAcceptance = $internalCoordinatorAcceptance;

        return $this;
    }

    /**
     * Get internalCoordinatorAcceptance
     *
     * @return bool
     */
    public function getInternalCoordinatorAcceptance()
    {
        return $this->internalCoordinatorAcceptance;
    }

    /**
     * Set externalCoordinatorAcceptance
     *
     * @param boolean $externalCoordinatorAcceptance
     *
     * @return StudentEnrolledCourse
     */
    public function setExternalCoordinatorAcceptance($externalCoordinatorAcceptance)
    {
        $this->externalCoordinatorAcceptance = $externalCoordinatorAcceptance;

        return $this;
    }

    /**
     * Get externalCoordinatorAcceptance
     *
     * @return bool
     */
    public function getExternalCoordinatorAcceptance()
    {
        return $this->externalCoordinatorAcceptance;
    }

    /**
     * Set deansOfficeAcceptance
     *
     * @param string $deansOfficeAcceptance
     *
     * @return StudentEnrolledCourse
     */
    public function setDeansOfficeAcceptance($deansOfficeAcceptance)
    {
        $this->deansOfficeAcceptance = $deansOfficeAcceptance;

        return $this;
    }

    /**
     * Get deansOfficeAcceptance
     *
     * @return string
     */
    public function getDeansOfficeAcceptance()
    {
        return $this->deansOfficeAcceptance;
    }
}

