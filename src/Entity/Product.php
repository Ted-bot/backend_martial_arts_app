<?php

namespace App\Entity;

use DateTimeZone;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ProductRepository;
use App\Class\SkuGenerator;
use Doctrine\Persistence\ManagerRegistry;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    protected ManagerRegistry $em;

    #[ORM\Column(length: 10)]
    protected ?string $sku = null;

    // protected $categoryRepo;
    // protected $subscriptionTypeRepo;
    
    #[ORM\Column(length: 100)]
    protected ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 17, scale: 2)]
    protected ?string $price = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Category $category = null;

    #[ORM\Column(length: 510)]
    private ?string $description = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $images = null;

    #[ORM\Column]
    protected ?bool $isPublished = true;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'relatedProducts')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?CurrencyType $currency = null;

    #[ORM\ManyToOne(inversedBy: 'relatedSubscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SubscriptionType $duration = null;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $relatedUser = null;

    /**
     * @var Collection<int, ProductVat>
     */
    #[ORM\OneToMany(targetEntity: ProductVat::class, mappedBy: 'product')]
    protected Collection $productVats;

    /**
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'product')]
    private Collection $orderLines;

    public function __construct(
    )
    {
        $dateTime = new DateTimeImmutable();
        $this->createdAt = $dateTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        $this->productVats = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
        // $this->em = new ManagerRegistry();
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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
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

    public function getCurrencyType(): ?CurrencyType
    {
        return $this->currency;
    }

    public function setCurrencyType(?CurrencyType $currency): static
    {
        $this->currency = $currency;

        return $this;
    }

    public function getDuration(): ?SubscriptionType
    {
        return $this->duration;
    }

    public function setDuration(?SubscriptionType $duration): static
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
}
