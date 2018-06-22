<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CourseStudent
 *
 * @ORM\Table(name="course_student")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CourseStudentRepository")
 * @UniqueEntity(
 *     fields={"course, student"},
 *     message="Student is already signed for this course"
 * )
 */
class CourseStudent
{

    public function __construct($course, $student){

        $this->course = $course;
        $this->student = $student;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="course_student_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $course_student_id;

    /**
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="courseStudents")
     * @ORM\JoinColumn(name="course_id", referencedColumnName="course_id", onDelete="CASCADE")
     */
    private $course;

    /**
     * @ORM\ManyToOne(targetEntity="Student", inversedBy="courseStudents")
     * @ORM\JoinColumn(name="student_id", referencedColumnName="student_id", onDelete="CASCADE")
     */
    private $student;

    /**
     * @var string
     *
     * @ORM\Column(name="text_note", type="string", length=255, nullable=true)
     */
    private $textNote;

    /**
     * @var string
     *
     * @ORM\Column(name="grade", type="decimal", precision=2, scale=1, nullable=true)
     */
    private $grade;

    /**
     * @ORM\ManyToOne(targetEntity="State")
     * @ORM\JoinColumn(name="state", referencedColumnName="state_id", onDelete="CASCADE")
     */
    private $state;

    /**
     * @Assert\DateTime()
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $person;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->course_student_id;
    }

    /**
     * Set course
     *
     * @param integer $course
     *
     * @return CourseStudent
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
     * @return CourseStudent
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
     * Set student
     *
     * @param string $textNote
     *
     * @return CourseStudent
     */
    public function setTextNote($textNote)
    {
        $this->textNote = $textNote;

        return $this;
    }

    /**
     * Get textNote
     *
     * @return string
     */
    public function getTextNote()
    {
        return $this->textNote;
    }

    /**
     * Set grade
     *
     * @param integer $grade
     *
     * @return CourseStudent
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return int
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * Set state
     *
     * @param string $state
     *
     * @return CourseStudent
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set date
     *
     * @param string $date
     *
     * @return CourseStudent
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set person
     *
     * @param string $person
     *
     * @return CourseStudent
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
}

