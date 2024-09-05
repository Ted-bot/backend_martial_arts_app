<?php

namespace App\Entity;

use DateTimeZone;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\MolliePaymentStatusEnum;
use App\Repository\StatusTransferRepository;

#[ORM\Entity(repositoryClass: StatusTransferRepository::class)]
class StatusTransfer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'statusTransfers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ShopOrder $userOrder;

    // #[ORM\Column(type: 'App\Entity\Enum\MolliePaymentStatusEnum')]
    #[ORM\Column(enumType: MolliePaymentStatusEnum::class)]
    private ?MolliePaymentStatusEnum $status = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 15)]
    private ?string $transferId = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $customer = null;

    public function __construct()
    {
        $this->status = MolliePaymentStatusEnum::OPEN;
        $dateTime = new DateTimeImmutable();
        $this->createdAt = $dateTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserOrder(): ?ShopOrder
    {
        return $this->userOrder;
    }

    public function setUserOrder(?ShopOrder $userOrder): static
    {
        $this->userOrder = $userOrder;

        return $this;
    }

    public function getStatus(): ?MolliePaymentStatusEnum
    {
        return $this->status;
    }

    public function setStatus(?MolliePaymentStatusEnum $status): static
    {
        $this->status = $status;

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

    public function getTransferId(): ?string
    {
        return $this->transferId;
    }

    public function setTransferId(string $transferId): static
    {
        $this->transferId = $transferId;

        return $this;
    }

    public function getCustomer(): ?string
    {
        return $this->customer;
    }

    public function setCustomer(?string $customer): static
    {
        $this->customer = $customer;

        return $this;
    }
}
