<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserProfileRepository;

#[ApiResource(
    description: 'Profile Entity',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => ['profile:read']
    ],
    denormalizationContext: [
        'groups' => ['profile:write']
    ],
    
)]
#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;


    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $websiteUrl = null;

    #[ORM\OneToOne(inversedBy: 'userProfile', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userUniq = null;

    #[ORM\ManyToOne(inversedBy: 'profileGroup')]
    private ?Group $group_student = null;

    #[ORM\ManyToOne(inversedBy: 'user_profile_create_post_event')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PostEvent $postEvent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

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

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): static
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getUserUniq(): ?User
    {
        return $this->userUniq;
    }

    public function setUserUniq(?User $userUniq): static
    {
        $this->userUniq = $userUniq;

        return $this;
    }

    public function getGroupStudent(): ?Group
    {
        return $this->group_student;
    }

    public function setGroupStudent(?Group $group_student): static
    {
        $this->group_student = $group_student;

        return $this;
    }

    public function getPostEvent(): ?PostEvent
    {
        return $this->postEvent;
    }

    public function setPostEvent(?PostEvent $postEvent): static
    {
        $this->postEvent = $postEvent;

        return $this;
    }
}
