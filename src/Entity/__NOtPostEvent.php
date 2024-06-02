<?php

// namespace App\Entity;

// use ApiPlatform\Metadata\Get;
// use ApiPlatform\Metadata\Put;
// use ApiPlatform\Metadata\Post;
// use ApiPlatform\Metadata\Patch;
// use ApiPlatform\Metadata\Delete;
// use Doctrine\ORM\Mapping as ORM;
// use ApiPlatform\Metadata\ApiResource;
// use ApiPlatform\Metadata\GetCollection;
// use App\Repository\PostEventRepository;
// use Doctrine\Common\Collections\Collection;
// use Doctrine\Common\Collections\ArrayCollection;
// use Symfony\Component\Serializer\Attribute\Groups;

// #[ORM\Entity(repositoryClass: PostEventRepository::class)]
// #[ApiResource(
//     shortName: 'trainingsession',
//     description: 'Calendar Post Entity',
//     operations: [
//         new Get(),
//         new GetCollection(),
//         new Post(),
//         new Patch(),
//         new Put(),
//         new Delete(),
//     ],
//     normalizationContext: [
//         'groups' => ['trainingsession:read']
//     ],
//     denormalizationContext: [
//         'groups' => ['trainingsession:write']
//     ],
    
// )]
// class PostEvent
// {
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column]
//     private ?int $id = null;

//     #[ORM\Column(length: 50)]
//     #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
//     private ?string $title = null;

//     #[Groups(['trainingsession:read', 'trainingsession:write','profile:read'])]
//     #[ORM\Column(length: 255, nullable: true)]
//     private ?string $description = null;

//     /**
//      * @var Collection<int, UserProfile>
//      */
//     #[ORM\OneToMany(targetEntity: UserProfile::class, mappedBy: 'postEvent')]
//     private Collection $user_profile_create_post_event;

//     #[ORM\OneToOne(inversedBy: 'singlePostEvent', cascade: ['persist', 'remove'])]
//     #[ORM\JoinColumn(nullable: false)]
//     private ?UserProfile $relatedUser = null;

//     public function __construct()
//     {
//         $this->user_profile_create_post_event = new ArrayCollection();
//     }

//     #[Groups('trainingsession:read')]
//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     #[Groups('trainingsession:read')]
//     public function getTitle(): ?string
//     {
//         return $this->title;
//     }

//     #[Groups('trainingsession:write')]
//     public function setTitle(string $title): static
//     {
//         $this->title = $title;

//         return $this;
//     }

//     #[Groups('trainingsession:read')]
//     public function getDescription(): ?string
//     {
//         return $this->description;
//     }

//     public function setDescription(?string $description): static
//     {
//         $this->description = nl2br($description);

//         return $this;
//     }

//     #[Groups('trainingsession:write')]
//     public function setTextDescription(?string $description): static
//     {
//         $this->description = nl2br($description);

//         return $this;
//     }

//     /**
//      * @return Collection<int, UserProfile>
//      */
//     #[Groups('trainingsession:read')]
//     public function getUserProfileCreatePostEvent(): Collection
//     {
//         return $this->user_profile_create_post_event;
//     }

//     public function addUserProfileCreatePostEvent(UserProfile $userProfileCreatePostEvent): static
//     {
//         if (!$this->user_profile_create_post_event->contains($userProfileCreatePostEvent)) {
//             $this->user_profile_create_post_event->add($userProfileCreatePostEvent);
//             $userProfileCreatePostEvent->setSinglePostEvent($this);
//         }

//         return $this;
//     }

//     // public function removeUserProfileCreatePostEvent(UserProfile $userProfileCreatePostEvent): static
//     // {
//     //     if ($this->user_profile_create_post_event->removeElement($userProfileCreatePostEvent)) {
//     //         // set the owning side to null (unless already changed)
//     //         if ($userProfileCreatePostEvent->getSinglePostEvent() === $this) {
//     //             $userProfileCreatePostEvent->setSinglePostEvent(false);
//     //         }
//     //     }

//     //     return $this;
//     // }

//     public function getRelatedUser(): ?UserProfile
//     {
//         return $this->relatedUser;
//     }

//     public function setRelatedUser(UserProfile $relatedUser): static
//     {
//         $this->relatedUser = $relatedUser;

//         return $this;
//     }
// }
