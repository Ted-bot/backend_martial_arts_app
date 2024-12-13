<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VatRateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VatRateRepository::class)]
class VatRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2)]    
    private ?string $procent = null;

    /**
     * @var Collection<int, ProductVat>
     */
    #[ORM\OneToMany(targetEntity: ProductVat::class, mappedBy: 'VatRate')]
    private Collection $relatedProductVat;

    public function __construct()
    {
        $this->relatedProductVat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProcent(): ?string
    {
        return $this->procent;
    }

    public function setProcent(string $procent): static
    {
        $this->procent = $procent;

        return $this;
    }

    /**
     * @return Collection<int, ProductVat>
     */
    public function getRelatedProductVat(): Collection
    {
        return $this->relatedProductVat;
    }

    public function addRelatedProductVat(ProductVat $relatedProductVat): static
    {
        if (!$this->relatedProductVat->contains($relatedProductVat)) {
            $this->relatedProductVat->add($relatedProductVat);
            $relatedProductVat->setVatRate($this);
        }

        return $this;
    }

    public function removeRelatedProductVat(ProductVat $relatedProductVat): static
    {
        if ($this->relatedProductVat->removeElement($relatedProductVat)) {
            // set the owning side to null (unless already changed)
            if ($relatedProductVat->getVatRate() === $this) {
                $relatedProductVat->setVatRate(null);
            }
        }

        return $this;
    }
}
