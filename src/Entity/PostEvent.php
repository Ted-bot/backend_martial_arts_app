<?php

namespace App\Entity;

use DateTimeZone;
use DateTimeImmutable;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\PostEventRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PostEventRepository::class)]
#[ApiResource(
    shortName: 'trainingsession',
    description: 'Calendar Post Entity',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => ['trainingsession:read']
    ],
    denormalizationContext: [
        'groups' => ['trainingsession:write']
    ],
    
)]
class PostEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
    public ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
    private ?bool $isPublished = true;

    #[ORM\ManyToOne(inversedBy: 'postEvents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?UserProfile $relatedUser = null;

    #[ORM\Column]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?bool $allDay = null;

    /**
     * @var Collection<int, UserProfile>
     */
    #[ORM\ManyToMany(targetEntity: UserProfile::class, inversedBy: 'subscribeToEvents')]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private Collection $subscribe;

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

    #[Groups('trainingsession:write')]
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

    #[Groups('trainingsession:write')]
    public function setRelatedUser(?UserProfile $relatedUser): static
    {
        $this->relatedUser = $relatedUser;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    #[Groups('trainingsession:write')]
    public function setStartDate(?\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    #[Groups('trainingsession:write')]
    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function isAllDay(): ?bool
    {
        return $this->allDay;
    }

    #[Groups('trainingsession:write')]
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
        $this->subscribe->removeElement($subscribe);

        return $this;
    }
}
