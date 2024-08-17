<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\SubscriptionTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubscriptionTypeRepository::class)]
#[ApiResource]
class SubscriptionType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 12)]
    private ?string $duration = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'durationId')]
    private Collection $relatedSubscriptions;

    public function __construct()
    {
        $this->relatedSubscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getRelatedSubscriptions(): Collection
    {
        return $this->relatedSubscriptions;
    }

    public function setRelatedSubscription(Product $relatedSubscription): static
    {
        if (!$this->relatedSubscriptions->contains($relatedSubscription)) {
            $this->relatedSubscriptions->add($relatedSubscription);
            $relatedSubscription->setDuration($this);
        }

        return $this;
    }

    public function deleteRelatedSubscription(Product $relatedSubscription): static
    {
        if ($this->relatedSubscriptions->removeElement($relatedSubscription)) {
            // set the owning side to null (unless already changed)
            if ($relatedSubscription->getDuration() === $this) {
                $relatedSubscription->setDuration(null);
            }
        }

        return $this;
    }
}
