<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\PostEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostEventRepository::class)]
#[ApiResource]
class PostEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, UserProfile>
     */
    #[ORM\OneToMany(targetEntity: UserProfile::class, mappedBy: 'postEvent')]
    private Collection $user_profile_create_post_event;

    public function __construct()
    {
        $this->user_profile_create_post_event = new ArrayCollection();
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

    /**
     * @return Collection<int, UserProfile>
     */
    public function getUserProfileCreatePostEvent(): Collection
    {
        return $this->user_profile_create_post_event;
    }

    public function addUserProfileCreatePostEvent(UserProfile $userProfileCreatePostEvent): static
    {
        if (!$this->user_profile_create_post_event->contains($userProfileCreatePostEvent)) {
            $this->user_profile_create_post_event->add($userProfileCreatePostEvent);
            $userProfileCreatePostEvent->setPostEvent($this);
        }

        return $this;
    }

    public function removeUserProfileCreatePostEvent(UserProfile $userProfileCreatePostEvent): static
    {
        if ($this->user_profile_create_post_event->removeElement($userProfileCreatePostEvent)) {
            // set the owning side to null (unless already changed)
            if ($userProfileCreatePostEvent->getPostEvent() === $this) {
                $userProfileCreatePostEvent->setPostEvent(null);
            }
        }

        return $this;
    }
}
