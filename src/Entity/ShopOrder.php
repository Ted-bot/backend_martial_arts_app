<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\ShopOrderRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ShopOrderRepository::class)]
#[ApiResource(
    shortName: 'shopOrder',
    description: 'User ShopOrder',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Patch(),
    ],
    normalizationContext: [
        'groups' => ['shopOrder:read']
    ],
    denormalizationContext: [
        'groups' => ['traishopOrder:write']
    ],
)]
class ShopOrder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'shopOrders')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?User $ownedBy = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 17, scale: 2)]
    protected ?string $totalAmount = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected ?\DateTimeInterface $orderDate = null;

    #[ORM\ManyToOne(inversedBy: 'shopOrders')]
    protected ?UserAddress $shippingAddress = null;

    #[ORM\ManyToOne(inversedBy: 'shopOrders')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?OrderStatus $orderStatus = null;

    /**
     * @var Collection<int, OrderLine>
     */
    #[ORM\OneToMany(targetEntity: OrderLine::class, mappedBy: 'shopOrder')]
    #[Groups(['shopOrder:read', 'orderline:read'])]
    protected Collection $orderLines;

    /**
     * @var Collection<int, StatusTransfer>
     */
    #[ORM\OneToMany(targetEntity: StatusTransfer::class, mappedBy: 'orderId')]
    private Collection $statusTransfers;

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
        $this->statusTransfers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnedBy(): ?User
    {
        return $this->ownedBy;
    }

    public function setOwnedBy(?User $ownedBy): static
    {
        $this->ownedBy = $ownedBy;

        return $this;
    }

    public function getTotalAmount(): ?string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getOrderDate(): ?\DateTimeInterface
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeInterface $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function getShippingAddress(): ?UserAddress
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?UserAddress $shippingAddress): static
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    public function getOrderStatus(): ?OrderStatus
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(?OrderStatus $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    /**
         * [Groups({"user:read"})]
        * @SerializedName("cheeseListings")
         */
        #[Groups(['trainingsession:read', 'profile:read'])]        
        public function getOrderLinesListings(): Collection
        {
            return $this->orderLines;
        }

    public function addOrderLine(OrderLine $orderLine): static
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setShopOrder($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): static
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getShopOrder() === $this) {
                $orderLine->setShopOrder(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, StatusTransfer>
     */
    public function getStatusTransfers(): Collection
    {
        return $this->statusTransfers;
    }

    public function addStatusTransfer(StatusTransfer $statusTransfer): static
    {
        if (!$this->statusTransfers->contains($statusTransfer)) {
            $this->statusTransfers->add($statusTransfer);
            $statusTransfer->setOrderId($this);
        }

        return $this;
    }

    public function removeStatusTransfer(StatusTransfer $statusTransfer): static
    {
        if ($this->statusTransfers->removeElement($statusTransfer)) {
            // set the owning side to null (unless already changed)
            if ($statusTransfer->getOrderId() === $this) {
                $statusTransfer->setOrderId(null);
            }
        }

        return $this;
    }
}
