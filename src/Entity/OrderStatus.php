<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\OrderStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderStatusRepository::class)]
#[ApiResource]
class OrderStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 15)]
    private ?string $status = null;

    /**
     * @var Collection<int, ShopOrder>
     */
    #[ORM\OneToMany(targetEntity: ShopOrder::class, mappedBy: 'orderStatus')]
    private Collection $shopOrders;

    public function __construct()
    {
        $this->shopOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, ShopOrder>
     */
    public function getShopOrders(): Collection
    {
        return $this->shopOrders;
    }

    public function addShopOrder(ShopOrder $shopOrder): static
    {
        if (!$this->shopOrders->contains($shopOrder)) {
            $this->shopOrders->add($shopOrder);
            $shopOrder->setOrderStatus($this);
        }

        return $this;
    }

    public function removeShopOrder(ShopOrder $shopOrder): static
    {
        if ($this->shopOrders->removeElement($shopOrder)) {
            // set the owning side to null (unless already changed)
            if ($shopOrder->getOrderStatus() === $this) {
                $shopOrder->setOrderStatus(null);
            }
        }

        return $this;
    }
}
