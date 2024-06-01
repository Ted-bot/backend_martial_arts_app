<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
#[ApiResource]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $code = null;

    /**
     * @var Collection<int, UserProfile>
     */
    #[ORM\OneToMany(targetEntity: UserProfile::class, mappedBy: 'group_student')]
    private Collection $profileGroup;

    public function __construct()
    {
        $this->profileGroup = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return Collection<int, UserProfile>
     */
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
