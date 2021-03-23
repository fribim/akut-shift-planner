<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Shift
 *
 * @ORM\Table(name="shift")
 * @ORM\Entity(repositoryClass="App\Repository\ShiftRepository")
 */
class Shift
{
    /**
     * @var int
     *
     * @ORM\Column(type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 250,
     *      minMessage = "plan.title.min_length",
     *      maxMessage = "plan.title.max_length",
     * )
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 250,
     *      minMessage = "plan.description.min_length",
     *      maxMessage = "plan.description.max_length",
     * )
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     * @Assert\NotBlank()
     * @ORM\Column(name="end", type="datetime")
     */
    private $end;

    /**
     * @var int
     *  @Assert\Range(
     *      min = 1,
     *      max = 300,
     *      minMessage = "shift.numberPeople.min",
     *      maxMessage = "shift.numberPeople.max"
     * )
     * @Assert\NotBlank()
     * @ORM\Column(name="numberPeople", type="integer")
     */
    private $numberPeople;

    /**
     * @var int
     *
     * Indicates the order of all shifts
     *
     * @ORM\Column(name="orderIndex", type="integer", nullable=true)
     */
    private $orderIndex = 0;


    /**
     * @ORM\ManyToOne(targetEntity="Plan", inversedBy="shifts")
     * @ORM\JoinColumn(name="plan_id", referencedColumnName="id")
     */
    private $plan;

    /**
     * @ORM\OneToMany(targetEntity="Person", mappedBy="shift", cascade={"remove"}, orphanRemoval=true)
     */
    private $people;

    public function __construct()
    {
        $this->people = new ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     *
     * @return Shift
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Shift
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Shift
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Shift
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set numberPeople
     *
     * @param integer $numberPeople
     *
     * @return Shift
     */
    public function setNumberPeople($numberPeople)
    {
        $this->numberPeople = $numberPeople;

        return $this;
    }

    /**
     * Get numberPeople
     *
     * @return int
     */
    public function getNumberPeople()
    {
        return $this->numberPeople;
    }

    /**
     * Set plan
     *
     * @param \App\Entity\Plan $plan
     *
     * @return Shift
     */
    public function setPlan(\App\Entity\Plan $plan = null)
    {
        $this->plan = $plan;

        return $this;
    }

    /**
     * Get plan
     *
     * @return \App\Entity\Plan
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * Add person
     *
     * @param \App\Entity\Person $person
     *
     * @return Shift
     */
    public function addPerson(\App\Entity\Person $person)
    {
        $this->people[] = $person;

        return $this;
    }

    /**
     * Remove person
     *
     * @param \App\Entity\Person $person
     */
    public function removePerson(\App\Entity\Person $person)
    {
        $this->people->removeElement($person);
    }

    /**
     * Get people
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPeople()
    {
        return $this->people;
    }

    /**
     * @return int
     */
    public function getOrderIndex()
    {
        return $this->orderIndex;
    }

    /**
     * @param int $orderIndex
     */
    public function setOrderIndex($orderIndex)
    {
        $this->orderIndex = $orderIndex;
    }


    public function __toString() {
        return $this->title;
    }
}
