<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductVatRepository;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ProductVatRepository::class)]
#[ApiResource]
class ProductVat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'relatedProductVat')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VatRate $VatRate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 17, scale: 4)]
    #[Groups(['profile:read'])]
    protected ?string $VatAmount = null;

    #[ORM\ManyToOne(inversedBy: 'productVats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVatRate(): ?VatRate
    {
        return $this->VatRate;
    }

    public function setVatRate(?VatRate $VatRate): static
    {
        $this->VatRate = $VatRate;

        return $this;
    }

    public function getVatAmount(): ?string
    {
        return $this->VatAmount;
    }

    public function setVatAmount(string $VatAmount): static
    {
        $this->VatAmount = $VatAmount;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
