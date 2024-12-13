<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['class:read', 'class:write','profile:read'])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(['class:read', 'class:write','profile:read'])]
    private ?int $code = null;

    /**
     * @var Collection<int, UserProfile>
     */
    #[ORM\OneToMany(targetEntity: UserProfile::class, mappedBy: 'groupStudent')]
    private Collection $profileGroup;

    public function __construct()
    {
        $this->profileGroup = new ArrayCollection();
    }

    #[Groups(['trainingsession:read', 'profile:read'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['trainingsession:read','profile:read'])]
    public function getName(): ?string
    {
        return $this->name;
    }

    #[Groups('trainingsession:write')]
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    #[Groups('trainingsession:read')]
    public function getCode(): ?int
    {
        return $this->code;
    }

    #[Groups('trainingsession:write')]
    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, UserProfile>
     */
    #[Groups(['trainingsession:read', 'profile:read'])]
    public function getProfileGroup(): Collection
    {
        return $this->profileGroup;
    }

    public function addProfileGroup(UserProfile $profileGroup): static
    {
        if (!$this->profileGroup->contains($profileGroup)) {
            $this->profileGroup->add($profileGroup);
            $profileGroup->setGroupStudent($this);
        }

        return $this;
    }

    public function removeProfileGroup(UserProfile $profileGroup): static
    {
        if ($this->profileGroup->removeElement($profileGroup)) {
            // set the owning side to null (unless already changed)
            if ($profileGroup->getGroupStudent() === $this) {
                $profileGroup->setGroupStudent(null);
            }
        }

        return $this;
    }
}
