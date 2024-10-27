<?php

namespace App\Entity;

use App\Enum\CountryTypeEnum;
use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $unitNumber = null;

    #[ORM\Column]
    private ?int $streetNumber = null;

    #[ORM\Column(length: 100)]
    private ?string $addressLine = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $region = null;

    #[ORM\Column(length: 10)]
    private ?string $postalCode = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Column(enumType: CountryTypeEnum::class)]
    private ?CountryTypeEnum $country = null;

    /**
     * @var Collection<int, UserAddress>
     */
    #[ORM\OneToMany(targetEntity: UserAddress::class, mappedBy: 'address')]
    private Collection $userAddresses;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $libReactCity = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $libReactState = null;

    public function __construct()
    {
        $this->userAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUnitNumber(): ?string
    {
        return $this->unitNumber;
    }

    public function setUnitNumber(?string $unitNumber): static
    {
        $this->unitNumber = $unitNumber;

        return $this;
    }

    public function getStreetNumber(): ?int
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(int $streetNumber): static
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getAddressLine(): ?string
    {
        return $this->addressLine;
    }

    public function setAddressLine(string $addressLine): static
    {
        $this->addressLine = $addressLine;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCountry(): ?CountryTypeEnum
    {
        return $this->country;
    }

    public function setCountry(?CountryTypeEnum $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, UserAddress>
     */
    public function getUserAddresses(): Collection
    {
        return $this->userAddresses;
    }

    public function addUserAddress(UserAddress $userAddress): static
    {
        if (!$this->userAddresses->contains($userAddress)) {
            $this->userAddresses->add($userAddress);
            $userAddress->setAddress($this);
        }

        return $this;
    }

    public function removeUserAddress(UserAddress $userAddress): static
    {
        if ($this->userAddresses->removeElement($userAddress)) {
            // set the owning side to null (unless already changed)
            if ($userAddress->getAddress() === $this) {
                $userAddress->setAddress(null);
            }
        }

        return $this;
    }

    public function getLibReactCity(): ?string
    {
        return $this->libReactCity;
    }

    public function setLibReactCity(?string $libReactCity): static
    {
        $this->libReactCity = $libReactCity;

        return $this;
    }

    public function getLibReactState(): ?string
    {
        return $this->libReactState;
    }

    public function setLibReactState(?string $libReactState): static
    {
        $this->libReactState = $libReactState;

        return $this;
    }
}
