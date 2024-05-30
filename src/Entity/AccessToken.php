<?php

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken
{
    private const PERSONAL_ACCES_TOKEN_PREFIX = 'tcp_';

    public const SCOPE_USER_STUDENT = 'ROLE_USER_STUDENT';
    public const SCOPE_USER_EXPERT = 'ROLE_USER_EXPERT';
    public const SCOPE_USER_SIFU = 'ROLE_USER_SIFU';

    public const SCOPES = [
        self::SCOPE_USER_STUDENT => 'User Student',
        self::SCOPE_USER_EXPERT => 'User Expert',
        self::SCOPE_USER_SIFU => 'User Sifu'
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'accessTokens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owendBy = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $expiresAt = null;

    #[ORM\Column(length: 68)]
    private string $token;

    #[ORM\Column]
    private array $scopes = [];

    public function __construct(string $tokenType = self::PERSONAL_ACCES_TOKEN_PREFIX)
    {
        $this->token = $tokenType.bin2hex(random_bytes(32));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwendBy(): ?User
    {
        return $this->owendBy;
    }

    public function setOwendBy(?User $owendBy): static
    {
        $this->owendBy = $owendBy;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeImmutable $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): static
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function isValid(): bool
    {
        return $this->expiresAt === null || $this->expiresAt > new \DateTimeImmutable();
    }
}
