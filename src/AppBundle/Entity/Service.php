<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="service")
 * @ORM\Entity
 */
class Service
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\Column(name="maintainer", type="string", length=255)
     */
    private $dataMaintainer;

    /**
     * @ORM\Column(name="endDate", type="date")
     */
    private $endDate;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Stage")
     */
    private $stages;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Category")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\ServiceUser")
     */
    private $serviceUsers;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Issue")
     */
    private $issues;

    function __construct($dataMaintainer, $description, $endDate, $name)
    {
        $this->dataMaintainer = $dataMaintainer;
        $this->description = $description;
        $this->endDate = $endDate;
        $this->name = $name;
        $this->stages = new ArrayCollection();
    }

    public function setDataMaintainer($dataMaintainer)
    {
        $this->dataMaintainer = $dataMaintainer;
    }

    public function getDataMaintainer()
    {
        return $this->dataMaintainer;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStages()
    {
        return $this->stages;
    }

    public function setStages($stages)
    {
        $this->stages = $stages;
    }

    public function addStage($stage)
    {
        $this->stages[] = $stage;
    }

    public function removeStage($stage)
    {
        $this->stages->removeElement($stage);
    }

    public function __toString()
    {
        return $this->name;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function addCategory($category)
    {
        $this->categories[] = $category;
    }

    public function removeCategory($category)
    {
        $this->categories->removeElement($category);
    }

    public function setIssues($issues)
    {
        $this->issues = $issues;
    }

    public function getIssues()
    {
        return $this->issues;
    }

    public function addIssue($issue)
    {
        $this->issues[] = $issue;
    }

    public function removeIssue($issue)
    {
        $this->issues->removeElement($issue);
    }

    public function setServiceUsers($serviceUsers)
    {
        $this->serviceUsers = $serviceUsers;
    }

    public function getServiceUsers()
    {
        return $this->serviceUsers;
    }

    public function addServiceUser($serviceUser)
    {
        $this->serviceUsers[] = $serviceUser;
    }

    public function removeServiceUser($serviceUser)
    {
        $this->serviceUsers->removeElement($serviceUser);
    }
}

