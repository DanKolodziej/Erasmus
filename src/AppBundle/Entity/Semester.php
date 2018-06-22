<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Semester
 *
 * @ORM\Table(name="semester")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\semesterRepository")
 */
class Semester
{

    /**
     * Default constructor, initializes collections
     */
    public function __construct()
    {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="semester_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $semesterId;

    /**
     * @var string
     *
     * @ORM\Column(name="season", type="string", length=255)
     */
    private $season;

    /**
     * @var string
     *
     * @ORM\Column(name="year", type="string", length=255)
     */
    private $year;

    /**
     * @var string
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="semesters")
     */
    private $courses;


    /**
     * Get semesterId
     *
     * @return int
     */
    public function getId()
    {
        return $this->semesterId;
    }

    /**
     * Set season
     *
     * @param string $season
     *
     * @return semester
     */
    public function setSeason($season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return string
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
 * Set year
 *
 * @param string $year
 *
 * @return semester
 */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return semester
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param Course $course
     */
    public function addCourse(Course $course)
    {
        if ($this->courses->contains($course)) {
            return;
        }
        $this->courses->add($course);
        $course->addSemester($this);
    }
    /**
     * @param Course $course
     */
    public function removeCourse(Course $course)
    {
        if (!$this->courses->contains($course)) {
            return;
        }
        $this->courses->removeElement($course);
        $course->removeSemester($this);
    }

    /**
     * @return array
     */
    public function getCourses() {
        $courseArr = [];
        foreach ($this->courses as $course) {
            $courseArr[] = $course;
        }
        return $courseArr;
    }

    /**
     * Get course as ArrayCollection.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCoursesCollection() {
        return $this->courses;
    }

    /**
     * Set courses.
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $courses
     * @return semester
     */
    public function setCoursesCollection($courses) {
        $this->courses = $courses;
        return $this;
    }

    public function __toString()
    {
        return (string)$this->semesterId;
    }
}

