<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="provider")
 * @ORM\Entity
 */
class Provider
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $name;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(name="$phoneNumber", type="string", length=255, nullable=true)
     * @Assert\Length(max=255)
     */
    private $phone;

    /**
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;

    /**
     * @ORM\Column(name="facebook", type="string", length=255, nullable=true)
     */
    private $facebook;

    /**
     * @ORM\Column(name="twitter", type="string", length=255, nullable=true)
     */
    private $twitter;

    /**
     * @ORM\Column(name="contactName", type="string", length=255, nullable=true)
     */
    private $contactName;

    /**
     * @ORM\Column(name="postcode", type="string", length=20, nullable=true)
     */
    private $postcode;

    /**
     * @ORM\Column(name="address", type="text", nullable=true)
     */
    private $address;

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

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setContactName($contactName)
    {
        $this->contactName = $contactName;
    }

    public function getContactName()
    {
        return $this->contactName;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
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

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setPostcode($postcode)
    {
        $this->postcode = $postcode;
    }

    public function getPostcode()
    {
        return $this->postcode;
    }

    public function setWebsite($website)
    {
        $this->website = $website;
    }

    public function getWebsite()
    {
        return $this->website;
    }

    public function __toString()
    {
        return $this->name ?: '';
    }

    public function getFacebook()
    {
        return $this->facebook;
    }

    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    public function getTwitter()
    {
        return $this->twitter;
    }

    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    public function getLastReviewDate()
    {
        return $this->lastReviewDate;
    }

    public function setLastReviewDate($lastReviewDate)
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

    public function getLastReviewComments()
    {
        return $this->lastReviewComments;
    }

    public function setLastReviewComments($lastReviewComments)
    {
        $this->lastReviewComments = $lastReviewComments;
    }

    public function getNextReviewDate()
    {
        return $this->nextReviewDate;
    }

    public function setNextReviewDate($nextReviewDate)
    {
        $this->nextReviewDate = $nextReviewDate;
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
            return $this->lastReviewDate->format(\DateTime::ISO8601);
        }
        return null;
    }

    public function getISO8601NextReviewDate()
    {
        if ($this->nextReviewDate) {
            return $this->nextReviewDate->format(\DateTime::ISO8601);
        }
        return null;
    }

}

