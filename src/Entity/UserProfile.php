<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserProfileRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ApiResource(
    shortName: 'profile',
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
// #[ApiResource(
//     uriTemplate: '/profile/{profile_id}/trainingsession/.{_format}',
//     operations: [new GetCollection]
// )]
#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['profile:read','profile:write'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[Groups(['profile:read','profile:write'])]
    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $websiteUrl = null;

    #[ORM\OneToOne(inversedBy: 'userProfile', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['profile:read'])]
    private ?User $userUniq = null;

    #[Groups(['profile:read'])]
    #[ORM\ManyToOne(inversedBy: 'profileGroup')]
    private ?Group $groupStudent = null;

    /**
     * @var Collection<int, PostEvent>
     */
    #[Groups(['profile:read'])]
    #[ORM\OneToMany(targetEntity: PostEvent::class, mappedBy: 'relatedUser')]
    private Collection $postEvents;

    /**
     * @var Collection<int, PostEvent>
     */
    // #[Groups(['profile:read'])]
    #[ORM\ManyToMany(targetEntity: PostEvent::class, mappedBy: 'subscribe')]    
    private Collection $subscribeToEvents;

    /**
     * @var Collection<int, TokenManager>
     */
    #[Groups(['profile:read'])]
    #[ORM\OneToMany(targetEntity: TokenManager::class, mappedBy: 'userProfile')]
    private Collection $tokenManagers;

    // #[ORM\OneToOne(mappedBy: 'userProfile', cascade: ['persist', 'remove'])]
    // private ?TokenManager $tokenManager = null;

    public function __construct()
    {
        $this->postEvents = new ArrayCollection();
        $this->subscribeToEvents = new ArrayCollection();
        $this->tokenManagers = new ArrayCollection();
    }

    // #[ORM\ManyToOne(inversedBy: 'user_profile_create_post_event')]
    // #[ORM\JoinColumn(nullable: true)]
    // public ?PostEvent $getAllPostEvents = null;

    // #[ORM\OneToOne(mappedBy: 'relatedUser', cascade: ['persist', 'remove'])]
    // public ?PostEvent $singlePostEvent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->username;
    }

    public function setUserName(?string $username): static
    {
        $this->username = $username;

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

    public function getgroupStudent(): ?Group
    {
        return $this->groupStudent;
    }

    public function setgroupStudent(?Group $groupStudent): static
    {
        $this->groupStudent = $groupStudent;

        return $this;
    }

    // #[Groups('profile:read')]
    // public function getAllPostEvents(): ?PostEvent
    // {
    //     return $this->getAllPostEvents;
    // }

    // #[Groups('profile:write')] //? function might be removable
    // public function setAllPostEvents(?PostEvent $getAllPostEvents): static
    // {
    //     $this->getAllPostEvents = $getAllPostEvents;

    //     return $this;
    // }

    // #[Groups('profile:read')]
    // public function getSinglePostEvent(): ?PostEvent
    // {
    //     return $this->singlePostEvent;
    // }

    // #[Groups('profile:write')]
    // public function setSinglePostEvent(PostEvent $singlePostEvent): static
    // {
    //     // set the owning side of the relation if necessary
    //     if ($singlePostEvent->getRelatedUser() !== $this) {
    //         $singlePostEvent->setRelatedUser($this);
    //     }

    //     $this->singlePostEvent = $singlePostEvent;

    //     return $this;
    // }

    // /**
    //  * @return Collection<int, PostEvent>
    //  */
    // public function getPostEvents(): Collection
    // {
    //     return $this->postEvents;
    // }

    // public function addPostEvent(PostEvent $postEvent): static
    // {
    //     if (!$this->postEvents->contains($postEvent)) {
    //         $this->postEvents->add($postEvent);
    //         $postEvent->setRelatedUser($this);
    //     }

    //     return $this;
    // }

    // public function removePostEvent(PostEvent $postEvent): static
    // {
    //     if ($this->postEvents->removeElement($postEvent)) {
    //         // set the owning side to null (unless already changed)
    //         if ($postEvent->getRelatedUser() === $this) {
    //             $postEvent->setRelatedUser(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, PostEvent>
     */
    public function getSubscribeToEvents(): Collection
    {
        return $this->subscribeToEvents;
    }

    public function addSubscribeToEvent(PostEvent $subscribeToEvent): static
    {
        if (!$this->subscribeToEvents->contains($subscribeToEvent)) {
            $this->subscribeToEvents->add($subscribeToEvent);
            $subscribeToEvent->addSubscribe($this);
        }

        return $this;
    }

    public function removeSubscribeToEvent(PostEvent $subscribeToEvent): static
    {
        if ($this->subscribeToEvents->removeElement($subscribeToEvent)) {
            $subscribeToEvent->removeSubscribe($this);
        }

        return $this;
    }

    // public function getTokenManager(): ?TokenManager
    // {
    //     return $this->tokenManager;
    // }

    // public function setTokenManager(TokenManager $tokenManager): static
    // {
    //     // set the owning side of the relation if necessary
    //     if ($tokenManager->getUserProfile() !== $this) {
    //         $tokenManager->setUserProfile($this);
    //     }

    //     $this->tokenManager = $tokenManager;

    //     return $this;
    // }

    /**
     * @return Collection<int, TokenManager>
     */
    public function getTokenManagers(): Collection
    {
        return $this->tokenManagers;
    }

    public function addTokenManager(TokenManager $tokenManager): static
    {
        if (!$this->tokenManagers->contains($tokenManager)) {
            $this->tokenManagers->add($tokenManager);
            $tokenManager->setUserProfile($this);
        }

        return $this;
    }

    public function removeTokenManager(TokenManager $tokenManager): static
    {
        if ($this->tokenManagers->removeElement($tokenManager)) {
            // set the owning side to null (unless already changed)
            if ($tokenManager->getUserProfile() === $this) {
                $tokenManager->setUserProfile(null);
            }
        }

        return $this;
    }
}
