<?php

namespace App\Entity;

use DateTime;
use DateTimeZone;
use DateTimeImmutable;
use DateTimeInterface;
use App\Entity\UserProfile;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PostEventRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
// use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PostEventRepository::class)]
class PostEvent
{
    // #[Groups(['trainingsession:read', 'profile:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
    #[ORM\Column(length: 50)]
    public ?string $title = null;

    // #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    // #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
    #[ORM\Column]
    private ?bool $isPublished = true;

    // #[Groups(['trainingsession:read', 'profile:read'])]
    #[ORM\ManyToOne(inversedBy: 'postEvents')] // , fetch: 'EAGER'
    #[ORM\JoinColumn(nullable: false)]
    private ?UserProfile $relatedUser = null;

    // #[Groups(['trainingsession:read', 'profile:read'])]
    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    // #[Groups(['trainingsession:read', 'profile:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private DateTime $startDate; //DateTimeInterface|

    // #[Groups(['trainingsession:read', 'profile:read'])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private DateTime $endDate; //DateTimeInterface|

    // #[Groups(['trainingsession:read', 'profile:read'])]
    #[ORM\Column(nullable: true)]
    private ?bool $allDay = null;

    // #[Groups(['trainingsession:read', 'profile:read'])]
    //  * @var Collection<int, UserProfile>
    /**
     * @var ArrayCollection<int, UserProfile>
     */
    #[ORM\ManyToMany(targetEntity: UserProfile::class, inversedBy: 'subscribeToEvents')]
    private $subscribe;

    public function __construct()
    {
        $dateTime = new DateTimeImmutable();
        $this->createdAt = $dateTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        $this->subscribe = new ArrayCollection();
        $this->setPublished(true);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    // #[Groups('trainingsession:write')]
    public function setPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getPublished()
    {
        return $this->isPublished;
    }

    public function getRelatedUser(): ?UserProfile
    {
        return $this->relatedUser;
    }

    // #[Groups('trainingsession:write')]
    public function setRelatedUser(?UserProfile $relatedUser): static
    {
        $this->relatedUser = $relatedUser;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartDate(): DateTimeInterface|DateTime
    {
        return $this->startDate;
    }

    // #[Groups('trainingsession:write')]
    public function setStartDate(DateTime $startDate): static
    {
        $startDate = $startDate instanceof DateTime
        ? $startDate 
        : DateTimeImmutable::createFromMutable($startDate);  
        
        $this->startDate = $startDate;
        // $this->startDate = $startDate->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        return $this;
    }

    public function getEndDate(): DateTimeInterface|DateTime
    {
        return $this->endDate;
    }

    // #[Groups('trainingsession:write')]
    public function setEndDate(DateTime $endDate): static
    {
        $endDate = $endDate instanceof DateTime
        ? $endDate 
        : DateTimeImmutable::createFromMutable($endDate);   

        $this->endDate = $endDate;

        return $this;
    }

    public function isAllDay(): ?bool
    {
        return $this->allDay;
    }

    // #[Groups('trainingsession:write')]
    public function setAllDay(?bool $allDay): static
    {
        $this->allDay = $allDay;

        return $this;
    }

    /**
     * @return Collection<int, UserProfile>
     */
    public function getSubscribe(): Collection
    {
        return $this->subscribe;
    }

    public function addSubscribe(UserProfile $subscribe): static
    {
        if (!$this->subscribe->contains($subscribe)) {
            $this->subscribe->add($subscribe);
        }

        return $this;
    }

    // #Foundry setter function
    // public function setSubscribe(UserProfile $subscribe): static
    // {
    //     if (!$this->subscribe->contains($subscribe)) {
    //         $this->subscribe->add($subscribe);
    //     }

    //     return $this;
    // }

    public function removeSubscribe(UserProfile $subscribe): static
    {
        if (!$this->subscribe->contains($subscribe)) {
            $this->subscribe->removeElement($subscribe);
        }
        return $this;
    }
}
