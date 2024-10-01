<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserAddressRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: UserAddressRepository::class)]
#[ApiResource]
class UserAddress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userAddresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $addressUser = null;

    #[ORM\ManyToOne(inversedBy: 'userAddresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Address $address = null;

    #[ORM\Column]
    private ?bool $isDefault = null;

    /**
     * @var Collection<int, ShopOrder>
     */
    #[ORM\OneToMany(targetEntity: ShopOrder::class, mappedBy: 'shippingAddress')]
    private Collection $shopOrders;

    public function __construct()
    {
        $this->shopOrders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddressUser(): ?User
    {
        return $this->addressUser;
    }

    public function setAddressUser(?User $addressUser): static
    {
        $this->addressUser = $addressUser;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function isDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setDefault(bool $isDefault): static
    {
        $this->isDefault = $isDefault;

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
            $shopOrder->setShippingAddress($this);
        }

        return $this;
    }

    public function removeShopOrder(ShopOrder $shopOrder): static
    {
        if ($this->shopOrders->removeElement($shopOrder)) {
            // set the owning side to null (unless already changed)
            if ($shopOrder->getShippingAddress() === $this) {
                $shopOrder->setShippingAddress(null);
            }
        }

        return $this;
    }
}
