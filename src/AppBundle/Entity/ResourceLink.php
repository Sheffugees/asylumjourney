<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="resource")
 * @ORM\Entity
 */
class ResourceLink
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
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(name="expiryDate", type="date", nullable=true)
     * @Assert\Date()
     * @var DateTime
     */
    private $expiryDate;

    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="resources")
     */
    private $service;

    /**
     * @ORM\Column(name="comments", type="text", nullable=true)
     * @var string
     */
    private $comments;

    public function getService()
    {
        return $this->service;
    }

    public function setService(?Service $service)
    {
        $this->service = $service;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName(string $name): ResourceLink
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __toString() {
        return $this->name ?: '';
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl(string $url): ResourceLink
    {
        $this->url = $url;

        return $this;
    }

    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;
    }

    public function getISO8601ExpiryDate()
    {
        if ($this->expiryDate) {
            return $this->expiryDate->format(DateTime::ISO8601);
        }
        return null;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

}

