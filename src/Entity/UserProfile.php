<?php

namespace App\Entity;


use App\Entity\Subscription;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PostEventRepository;
use App\Repository\UserProfileRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: UserProfileRepository::class)]
class UserProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(length: 1024, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $websiteUrl = null;

    #[ORM\OneToOne(inversedBy: 'userProfile', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userUniq = null;

    #[ORM\ManyToOne(inversedBy: 'profileGroup')]
    private ?Group $groupStudent;
    
    /**
     * @var Collection<int, PostEvent>
     */
    #[ORM\OneToMany(targetEntity: PostEvent::class, mappedBy: 'relatedUser')] //, cascade: ['persist', 'remove'], orphanRemoval: true
    private Collection $postEvents;

    /**
     * @var Collection<int, PostEvent>
     */
    #[ORM\ManyToMany(targetEntity: PostEvent::class, mappedBy: 'subscribe', fetch: 'EXTRA_LAZY')]    
    private ?Collection $subscribeToEvents; // = null

    /**
     * @var Collection<int, TokenManager>
     */
    #[ORM\OneToMany(targetEntity: TokenManager::class, mappedBy: 'userProfile')]
    private Collection $tokenManagers;

    // note: could cause weird issues with logging in, try removing #[ORM\OneToOne(targetEntity:...
    // #[ORM\OneToOne(targetEntity: TokenManager::class, mappedBy: 'userProfile', cascade: ['persist', 'remove'])]
    // private ?TokenManager $tokenManager = null;

    // property subscription is defined in UserProfile, currently commented out #UserProfileEntityToDtoStateProvider
    // /** @var Subscription Non-persisted property */
    // private ?Subscription $subscription = null;
    
    private ?int $tokens = null;

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

    /** @return Collection<int, PostEvent> */
    public function getUserPublisedSubscribedEvents()
    {
        return $this->subscribeToEvents->matching(PostEventRepository::findUserSubscribedPublishedEventPostEvents());
    }
    

    // public function getUserSubscribedToEvents($id)
    // {
    //     // $postEventRepo = new PostEventRepository();
    //     $userSubscribedEvents = $this->subscribeToEvents->isPublished();
    //     // $userSubscribedEvents = $this->postEventRepository->findOneBy(['id' => $id]);
    //     return $userSubscribedEvents;
    // }

    // public function setAllPostEvents(?PostEvent $getAllPostEvents): static
    // {
    //     $this->getAllPostEvents = $getAllPostEvents;

    //     return $this;
    // }

    // public function getSinglePostEvent(): ?PostEvent
    // {
    //     return $this->singlePostEvent;
    // }

    // public function setSinglePostEvent(PostEvent $singlePostEvent): static
    // {
    //     // set the owning side of the relation if necessary
    //     if ($singlePostEvent->getRelatedUser() !== $this) {
    //         $singlePostEvent->setRelatedUser($this);
    //     }

    //     $this->singlePostEvent = $singlePostEvent;

    //     return $this;
    // }

    /**
     * @return Collection<int, PostEvent>
     */
    public function getPostEvents(): Collection
    {
        return $this->postEvents;
    }

    public function addPostEvent(PostEvent $postEvent): static
    {
        if (!$this->postEvents->contains($postEvent)) {
            $this->postEvents->add($postEvent);
            $postEvent->setRelatedUser($this);
        }

        return $this;
    }

    public function removePostEvent(PostEvent $postEvent): static
    {
        if ($this->postEvents->removeElement($postEvent)) {
            // set the owning side to null (unless already changed)
            if ($postEvent->getRelatedUser() === $this) {
                $postEvent->setRelatedUser(null);
            }
        }

        return $this;
    }

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

    // property subscription is defined in UserProfile, currently commented out #UserProfileEntityToDtoStateProvider
    // example from SymfonyCast api 3 but it fixxed by using $token to get tokens
    // public function getSubscription(): ?Subscription
    // {
    //     return $this->subscription;
    // }
    // // #[SerializedName('tokenManager')]
    // public function setSubscription(Subscription $subscription): static
    // {
    //     // set the owning side of the relation if necessary
    //     // if ($tokenManager->getUserProfile() !== $this) {
    //     //     $tokenManager->setUserProfile($this);
    //     // }
    //     // if (!isset($this->tokenManager)) {
    //     //     throw new \LogicException("You must call setTokenManger() before is getTokenManger");
    //     // }

    //     $this->subscription = $subscription;

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

    /**
     * Get the value of tokens
     */ 
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * Set the value of tokens
     *
     * @return  self
     */ 
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;

        return $this;
    }
}
