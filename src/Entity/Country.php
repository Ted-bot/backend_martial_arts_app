<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ApiResource]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // #[ORM\Column(length: 3)]
    #[ORM\Column]
    private ?string $code;
    
    // #[ORM\Column(length: 6)]
    #[ORM\Column]
    private ?string $locale;

    public function __construct($code, $locale)
    {
        $this->code = $code;
        $this->locale = $locale;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the value of locale
     */ 
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set the value of locale
     *
     * @return  self
     */ 
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }
}
