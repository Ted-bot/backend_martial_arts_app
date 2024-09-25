<?php

namespace App\Entity;

use App\Enum\SubscriptionDirectOrPeriodicTypeEnum;
use App\Enum\SubscriptionLengthTypeEnum;
use DateTimeZone;
use DateTimeImmutable;
use App\Entity\ProductVat;
use App\Class\SkuGenerator;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Metadata\Patch;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Enum\CategoryTypeEnum;
use App\Enum\CurrencyTypeEnum;
use App\Repository\ProductRepository;
use ApiPlatform\Metadata\GetCollection;
use App\Enum\SubscriptionTypeEnum;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\NumericFilter;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
// #[ApiResource]
#[ApiResource(
    shortName: 'Product',
    filters: ['app.product.search_filter'],
    description: 'Available Products',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
    ],
    normalizationContext: [
        'groups' => ['product:read']
    ],
    denormalizationContext: [
        'groups' => ['product:write']
    ],
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    #[Groups(['product:read'])]
    protected ?string $sku = null;   

    #[ORM\Column(length: 100)]
    #[Groups(['product:read'])]
    protected ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 17, scale: 2)]
    #[Groups(['product:read'])]
    protected ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Column(enumType: CategoryTypeEnum::class)]
    #[Groups(['product:read'])]
    protected ?CategoryTypeEnum $category;

    #[ORM\Column(length: 510)]
    #[Groups(['product:read'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    #[Groups(['product:read'])]
    private ?array $images = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    protected ?bool $isPublished = true;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'relatedProducts')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Column(enumType: CurrencyTypeEnum::class)]
    #[Groups(['product:read'])]
    protected ?CurrencyTypeEnum $currency;

    #[ORM\ManyToOne(inversedBy: 'relatedSubscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Column(enumType: SubscriptionTypeEnum::class)]
    #[Groups(['product:read'])]
    private ?SubscriptionTypeEnum $duration;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $relatedUser;

    /**
     * @var Collection<int, ProductVat>
     */
    #[ORM\OneToMany(targetEntity: ProductVat::class, mappedBy: 'product')]
    #[Groups(['product:read'])]
    protected Collection $productVats;

    /**
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'product')]
    #[Ignore]
    private Collection $orderLines;

    #[ORM\Column(enumType: SubscriptionLengthTypeEnum::class)]
    #[Groups(['product:read'])]
    private ?SubscriptionLengthTypeEnum $durationLength = null;

    #[ORM\Column]
    #[Groups(['product:read'])]
    private ?SubscriptionDirectOrPeriodicTypeEnum $directOrPeriodic;

    public function __construct(
    )
    {
        $dateTime = new DateTimeImmutable();
        $this->createdAt = $dateTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        $this->productVats = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
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

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?CategoryTypeEnum
    {
        return $this->category;
    }

    public function setCategory(?CategoryTypeEnum $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImages(): ?array
    {
        return $this->images;
    }

    public function setImages(?array $images): static
    {
        $this->images = $images;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCurrencyType(): ?CurrencyTypeEnum
    {
        return $this->currency;
    }

    public function setCurrencyType(?CurrencyTypeEnum $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getDuration(): ?SubscriptionTypeEnum
    {
        return $this->duration;
    }

    public function setDuration(?SubscriptionTypeEnum $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getRelatedUser(): ?User
    {
        return $this->relatedUser;
    }

    public function setRelatedUser(?User $relatedUser): static
    {
        $this->relatedUser = $relatedUser;

        return $this;
    }

    /**
     * @return Collection<int, ProductVat>
     */
    public function getProductVats(): Collection
    {
        return $this->productVats;
    }

    public function setProductVat(ProductVat $productVat): static
    {
        if (!$this->productVats->contains($productVat)) {
            $this->productVats->add($productVat);
            $productVat->setProduct($this);
        }

        return $this;
    }

    public function deleteProductVat(ProductVat $productVat): static
    {
        if ($this->productVats->removeElement($productVat)) {
            // set the owning side to null (unless already changed)
            if ($productVat->getProduct() === $this) {
                $productVat->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): static
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setProduct($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProduct() === $this) {
                $orderLine->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of sku
     */ 
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Set the value of sku
     *
     * @return  self
     */ 
    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    public function getDurationLength(): ?SubscriptionLengthTypeEnum
    {
        return $this->durationLength;
    }

    public function setDurationLength(?SubscriptionLengthTypeEnum $durationLength): static
    {
        $this->durationLength = $durationLength;

        return $this;
    }

    public function getDirectOrPeriodic(): ?SubscriptionDirectOrPeriodicTypeEnum
    {
        return $this->directOrPeriodic;
    }

    public function setDirectOrPeriodic(SubscriptionDirectOrPeriodicTypeEnum $directOrPeriodic): static
    {
        $this->directOrPeriodic = $directOrPeriodic;

        return $this;
    }
}
