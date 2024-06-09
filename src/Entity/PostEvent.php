<?php

namespace App\Entity;

use DateTimeZone;
use DateTimeImmutable;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
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
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
    private ?string $title = null;

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

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?\DateTimeInterface $eventDate = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?\DateTimeInterface $eventStart = null;
    
    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(['trainingsession:read', 'profile:read'])]
    private ?\DateTimeInterface $eventEnd = null;

    #[ORM\Column]
    private ?bool $eventRegular = null;

    public function __construct()
    {
        $dateTime = new DateTimeImmutable();
        $this->createdAt = $dateTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
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

    public function setPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getRelatedUser(): ?UserProfile
    {
        return $this->relatedUser;
    }

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

    public function getEventDate(): ?\DateTimeInterface
    {
        return $this->eventDate;
    }

    public function setEventDate(\DateTimeInterface $eventDate): static
    {
        $this->eventDate = $eventDate;

        return $this;
    }

    public function getEventStart(): ?\DateTimeInterface
    {
        return $this->eventStart;
    }
    
    public function getEventEnd(): ?\DateTimeInterface
    {
        return $this->eventEnd;
    }

    public function setEventStart(\DateTimeInterface $eventStart): static
    {
        $this->eventStart = $eventStart;

        return $this;
    }
    
    public function setEventEnd(\DateTimeInterface $eventEnd): static
    {
        $this->eventEnd = $eventEnd;

        return $this;
    }

    public function isEventRegular(): ?bool
    {
        return $this->eventRegular;
    }

    public function setEventRegular(bool $eventRegular): static
    {
        $this->eventRegular = $eventRegular;

        return $this;
    }
}
