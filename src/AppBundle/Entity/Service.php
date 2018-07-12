<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="service", indexes={@ORM\Index(name="hidden_idx", columns={"hidden"})})
 * @ORM\Entity
 */
class Service
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     * @var string
     */
    private $name;

    /**
     * @ORM\Column(name="hidden", type="boolean")
     * @var bool
     */
    private $hidden = false;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Assert\NotBlank()
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(name="maintainer", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     * @var string
     */
    private $dataMaintainer;

    /**
     * @ORM\Column(name="endDate", type="date", nullable=true)
     * @Assert\Date()
     * @var DateTime
     */
    private $endDate;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Provider")
     * @var Collection
     */
    private $providers;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Stage")
     * @var Collection
     */
    private $stages;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Category")
     * @var Collection
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\ServiceUser")
     * @var Collection
     */
    private $serviceUsers;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\Issue")
     * @var Collection
     */
    private $issues;

    /**
     * @ORM\Column(name="events", type="text", nullable=true)
     * @var string
     */
    private $events;

    /**
     * @ORM\ManyToMany(targetEntity="\AppBundle\Entity\ResourceLink", mappedBy="services", cascade={"all"})
     * @var Collection
     */
    private $resources;

    /**
     * @ORM\Column(name="lastReviewDate", type="date", nullable=true)
     * @Assert\Date()
     * @var DateTime
     */
    private $lastReviewDate;

    /**
     * @ORM\Column(name="lastReviewedBy", type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     * @var string
     */
    private $lastReviewedBy;

    /**
     * @ORM\Column(name="lastReviewComments", type="text", nullable=true)
     * @var string
     */
    private $lastReviewComments;

    /**
     * @ORM\Column(name="nextReviewDate", type="date", nullable=true)
     * @Assert\Date()
     * @var DateTime
     */
    private $nextReviewDate;

    /**
     * @ORM\Column(name="nextReviewComments", type="text", nullable=true)
     * @var string
     */
    private $nextReviewComments;

    /**
     * @ORM\Column(name="externalReviews", type="text", nullable=true)
     * @var string
     */
    private $externalReviews;

    public function __construct()
    {
        $this->stages = new ArrayCollection();
        $this->providers = new ArrayCollection();
        $this->issues = new ArrayCollection();
        $this->serviceUsers = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->resources = new ArrayCollection();
    }

    public function setDataMaintainer(?string $dataMaintainer)
    {
        $this->dataMaintainer = $dataMaintainer;
    }

    public function getDataMaintainer()
    {
        return $this->dataMaintainer;
    }

    public function setDescription(?string $description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setEndDate(?DateTime $endDate)
    {
        $this->endDate = $endDate;
    }

    public function getEndDate()
    {
        return $this->endDate;
    }

    public function getISO8601EndDate()
    {
        if ($this->endDate) {
            return $this->endDate->format(DateTime::ISO8601);
        }
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;
    }

    public function getHidden()
    {
        return $this->hidden;
    }

    public function getStages()
    {
        return $this->stages;
    }

    public function setStages(Collection $stages)
    {
        $this->stages = $stages;
    }

    public function addStage(Stage $newStage)
    {
        foreach ($this->stages as $stage) {
            if ($stage->getId() == $newStage->getId()) {
                return;
            }
        }

        $this->stages[] = $newStage;
    }

    public function removeStage(Stage $stage)
    {
        $this->stages->removeElement($stage);
    }

    public function __toString()
    {
        return $this->name ?: '';
    }

    public function setCategories(Collection $categories)
    {
        $this->categories = $categories;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function addCategory(Category $category)
    {
        $this->categories[] = $category;
    }

    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }

    public function setIssues(Collection $issues)
    {
        $this->issues = $issues;
    }

    public function getIssues()
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue)
    {
        $this->issues[] = $issue;
    }

    public function removeIssue(Issue $issue)
    {
        $this->issues->removeElement($issue);
    }

    public function setServiceUsers(Collection $serviceUsers)
    {
        $this->serviceUsers = $serviceUsers;
    }

    public function getServiceUsers()
    {
        return $this->serviceUsers;
    }

    public function addServiceUser(ServiceUser $serviceUser)
    {
        $this->serviceUsers[] = $serviceUser;
    }

    public function removeServiceUser(ServiceUser $serviceUser)
    {
        $this->serviceUsers->removeElement($serviceUser);
    }

    public function setProviders(Collection $providers)
    {
        $this->providers = $providers;
    }

    public function getProviders()
    {
        return $this->providers;
    }

    public function addProvider(Provider $provider)
    {
        $this->providers[] = $provider;
    }

    public function removeProvider(Provider $provider)
    {
        $this->providers->removeElement($provider);
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function setEvents($events)
    {
        $this->events = $events;
    }

    public function getResources()
    {
        return $this->resources;
    }

    public function setResources(Collection $resources)
    {
        foreach($this->resources as $existingResource) {
            $this->removeResource($existingResource);
        }

        foreach ($resources as $resource) {
            $this->addResource($resource);
        }
    }

    public function addResource(ResourceLink $resource)
    {
        $resource->addService($this);
        $this->resources[] = $resource;
    }

    public function removeResource(ResourceLink $resource)
    {
        $resource->removeService($this);
        $this->resources->removeElement($resource);
    }

    public function getLastReviewDate()
    {
        return $this->lastReviewDate;
    }

    public function setLastReviewDate(?DateTime $lastReviewDate)
    {
        $this->lastReviewDate = $lastReviewDate;
    }

    public function getLastReviewedBy()
    {
        return $this->lastReviewedBy;
    }

    public function setLastReviewedBy($lastReviewedBy)
    {
        $this->lastReviewedBy = $lastReviewedBy;
    }

    public function getNextReviewDate()
    {
        return $this->nextReviewDate;
    }

    public function setNextReviewDate(?DateTime $nextReviewDate)
    {
        $this->nextReviewDate = $nextReviewDate;
    }

    public function getLastReviewComments()
    {
        return $this->lastReviewComments;
    }

    public function setLastReviewComments($lastReviewComments)
    {
        $this->lastReviewComments = $lastReviewComments;
    }

    public function getNextReviewComments()
    {
        return $this->nextReviewComments;
    }

    public function setNextReviewComments($nextReviewComments)
    {
        $this->nextReviewComments = $nextReviewComments;
    }

    public function getISO8601LastReviewDate()
    {
        if ($this->lastReviewDate) {
            return $this->lastReviewDate->format(DateTime::ISO8601);
        }
        return null;
    }

    public function getISO8601NextReviewDate()
    {
        if ($this->nextReviewDate) {
            return $this->nextReviewDate->format(DateTime::ISO8601);
        }
        return null;
    }

    public function getExternalReviews()
    {
        return $this->externalReviews;
    }

    public function setExternalReviews($externalReviews)
    {
        $this->externalReviews = $externalReviews;
    }
}

