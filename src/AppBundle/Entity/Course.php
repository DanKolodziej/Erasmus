<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Course
 *
 * @ORM\Table(name="course")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CourseRepository")
 * @UniqueEntity(
 *     fields={"code"},
 *     message="There already is a course with this code"
 * )
 */
class Course
{
    /**
     * @var int
     *
     * @ORM\Column(name="course_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $courseId;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(message="A code is required for this course")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank(message="A name is required for this course")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="form", type="string", length=255)
     *
     * @Assert\NotBlank(message="A form is required for this course")
     */
    private $form;

    /**
     * @var int
     *
     * @ORM\Column(name="ECTS", type="integer")
     *
     * @Assert\NotBlank(message="A number of ECTS points is required for this course")
     *
     * @Assert\Range(
     *      min = 1,
     *      max = 30,
     *      minMessage = "A course must have at least 1 ECTS point",
     *      maxMessage = "A course can have 30 ECTS points max"
     * )
     */
    private $ects;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="string", length=255)
     *
     * @Assert\NotBlank(message="A level is required for this course")
     */
    private $level;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     *
     * @Assert\NotBlank(message="A type is required for this course")
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="syllabus", type="string", length=255, nullable=true)
     * @Assert\File(mimeTypes={ "application/pdf" })
     */
    private $syllabus;

    /**
     * @ORM\ManyToOne(targetEntity="InternalCoordinator")
     * @ORM\JoinColumn(name="internal_coordinator_id", referencedColumnName="internal_coordinator_id")
     */
    private $internalCoordinator;

    /**
     * @ORM\ManyToOne(targetEntity="Faculty")
     * @ORM\JoinColumn(name="faculty_id", referencedColumnName="faculty_id")
     */
    private $faculty;

    /**
     * @ORM\ManyToMany(targetEntity="Semester", inversedBy="courses")
     * @ORM\JoinTable(
     *  name="course_semester",
     *  joinColumns={
     *      @ORM\JoinColumn(name="course_id", referencedColumnName="course_id")
     *  },
     *  inverseJoinColumns={
     *      @ORM\JoinColumn(name="semester_id", referencedColumnName="semester_id")
     *  }
     * )
     *
     * @Assert\Count(
     *     min=1,
     *     minMessage="You must choose at least one semester"
     * )
     */
    private $semesters;

    /**
     * @ORM\OneToMany(targetEntity="CourseStudent", mappedBy="course")
     */
    private $courseStudents;

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->semesters = new \Doctrine\Common\Collections\ArrayCollection();
        $this->courseStudents = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get courseId
     *
     * @return int
     */
    public function getId()
    {
        return $this->courseId;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Course
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
     * Set name
     *
     * @param string $name
     *
     * @return Course
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
     * Set form
     *
     * @param string $form
     *
     * @return Course
     */
    public function setForm($form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get form
     *
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set eCTS
     *
     * @param integer $ects
     *
     * @return Course
     */
    public function setEcts($ects)
    {
        $this->ects = $ects;

        return $this;
    }

    /**
     * Get ects
     *
     * @return int
     */
    public function getEcts()
    {
        return $this->ects;
    }

    /**
     * Set level
     *
     * @param string $level
     *
     * @return Course
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Course
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set syllabus
     *
     * @param string $syllabus
     *
     * @return Course
     */
    public function setSyllabus($syllabus)
    {
        $this->syllabus = $syllabus;

        return $this;
    }

    /**
     * Get syllabus
     *
     * @return string
     */
    public function getSyllabus()
    {
        return $this->syllabus;
    }

    /**
     * Set internalCoordinator
     *
     * @param integer $internalCoordinator
     *
     * @return Course
     */
    public function setInternalCoordinator($internalCoordinator)
    {
        $this->internalCoordinator = $internalCoordinator;

        return $this;
    }

    /**
     * Get internalCoordinator
     *
     * @return int
     */
    public function getInternalCoordinator()
    {
        return $this->internalCoordinator;
    }

    /**
     * Set faculty
     *
     * @param integer $faculty
     *
     * @return Course
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

    /**
     * @param Semester $semester
     */
    public function addSemester(Semester $semester)
    {
        if ($this->semesters->contains($semester)) {
            return;
        }
        $this->semesters->add($semester);
        $semester->addCourse($this);
    }
    /**
     * @param Semester $semester
     */
    public function removeSemester(Semester $semester)
    {
        if (!$this->semesters->contains($semester)) {
            return;
        }
        $this->semesters->removeElement($semester);
        $semester->removeCourse($this);
    }

    /**
     * @return array
     */
    public function getSemesters() {
        $semesterArr = [];
        foreach ($this->semesters as $semester) {
            $semesterArr[] = $semester;
        }
        return $semesterArr;
    }

    /**
     * Get semesters as ArrayCollection.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSemestersCollection() {
        return $this->semesters;
    }

    /**
     * Set semesters.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $semesters
     * @return Course
     */
    public function setSemestersCollection($semesters) {
        $this->semesters = $semesters;
        return $this;
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
        $courseStudent->setCourse($this->courseId);
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
        $courseStudent->setCourse(null);
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
     * @return Course
     */
    public function setCourseStudentsCollection($courseStudents) {
        $this->courseStudents = $courseStudents;
        return $this;
    }

    public function __toString() {

        return $this->code.' '.$this->name;
    }
}

