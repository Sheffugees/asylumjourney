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
     * @ORM\OneToMany(targetEntity="\AppBundle\Entity\ResourceLink", mappedBy="service", cascade={"all"})
     * @var Collection
     */
    private $resources;

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
        foreach ($resources as $resource) {
            $resource->setService($this);
        }

        $this->resources = $resources;
    }
    
    public function addResource(ResourceLink $newResource)
    {
        $newResource->setService($this);
        foreach ($this->resources as $resource) {
            if ($resource->getId() == $newResource->getId()) {
                return;
            }
        }
    
        $this->resources[] = $newResource;
    }
    
    public function removeResource(ResourceLink $resource)
    {
        $this->stages->removeElement($resource);
    }

    
}

