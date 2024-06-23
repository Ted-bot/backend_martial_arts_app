<?php

namespace App\Entity;

use DateTimeZone;
use Carbon\Carbon;
use App\Class\Role;
use DateTimeImmutable;
use App\Entity\UserProfile;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Request\CreateUserRequest;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USER', fields: ['email', 'phone_number'])]
#[ApiResource(
    description: 'User Entity',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Patch(),
        new Put(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => ['user:read']
    ],
    denormalizationContext: [
        'groups' => ['user:write']
    ],
    
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read','profile:read'])]
    public ?int $id = null;

    /**
     * @var string Email of user
     */
    #[ORM\Column(length: 180)]
    #[Groups(['user:read', 'user:write','profile:read'])]
    public ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:read', 'user:write','profile:read'])]
    public array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Groups(['user:read'])]
    #[ORM\OneToOne(mappedBy: 'userUniq', targetEntity: UserProfile::class,cascade: ['persist', 'remove'])]
    public ?UserProfile $userProfile = null;

    /**
     * @var string Email of user
     */
    #[ORM\Column(length: 25)]
    #[Groups(['user:read', 'user:write','profile:read'])]
    public ?string $firstName = null;

    /**
     * @var string Lastname of user
     */
    #[ORM\Column(length: 25)]
    #[Groups(['user:read', 'user:write','profile:read'])]
    public ?string $lastName = null;

    /**
     * @var string Phonenumber of user
     */
    #[ORM\Column(length: 15)]
    #[Groups(['user:read', 'user:write','profile:read'])]
    public ?string $phoneNumber = null;

    /**
     * @var string A "Y-m-d H:i:s" formatted value
     */
    #[ORM\Column(length: 10)]
    #[Groups(['user:read', 'user:write','profile:read'])]
    public ?string $dateOfBirth = null;
        
    /**
     * @var string Gender of user
     */
    #[ORM\Column(length: 6)]
    #[Groups(['user:read', 'user:write','profile:read'])]
    public ?string $gender = null;

    /**
     * @var string Location of user
     */
    #[ORM\Column(length: 50)]
    #[Groups(['user:read', 'user:write', 'profile:read'])]
    public ?string $location = null;

    /**
     * @var string Conversion of user
     */
    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write', 'profile:read'])]
    protected ?string $conversion = null;

    /**
     * @var string datetime created account of user
     */
    #[ORM\Column(length: 25)]
    #[Groups(['user:read', 'user:write', 'profile:read'])]
    public ?DateTimeImmutable $createdAt;

    public function __construct()
    {
        $dateTime = new DateTimeImmutable();
        $this->createdAt = $dateTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        $this->setRoles([Role::ROLE_USER_STUDENT]);
        // $this->roles = ;
    }

    public function createNewUserObj(CreateUserRequest $user)
    {
        $this->setFirstName($user->firstName);
        $this->setLastName($user->lastName);
        $this->setEmail($user->email);
        $this->setDateOfBirth($user->dateOfBirth);

        $this->setPassword($user->password);
        
        $this->setPhoneNumber($user->phoneNumber);
        $this->setGender($user->gender);
        $this->setLocation($user->location);
        $this->setConversion($user->conversion);
        $this->setRoles([Role::ROLE_USER_STUDENT]);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return (string) $this->email;
    }

    #[Groups('user:write')]
    public function setEmail(string $email): static
    {
        $this->email = (string) $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */

    
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = Role::ROLE_USER_STUDENT;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    #[Groups('user:write')]
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    #[Groups('user:write')]
    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserProfile(): ?UserProfile
    {
        return $this->userProfile;
    }

    #[Groups('user:write')]
    public function setUserProfile(?UserProfile $userProfile): static
    {
        // unset the owning side of the relation if necessary
        if ($userProfile === null && $this->userProfile !== null) {
            $this->userProfile->setUserUniq(null);
        }

        // set the owning side of the relation if necessary
        if ($userProfile !== null && $userProfile->getUserUniq() !== $this) {
            $userProfile->setUserUniq($this);
        }

        $this->userProfile = $userProfile;

        return $this;
    }

    public function __toString()
    {
        return $this->getEmail();
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    #[Groups('user:write')]
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    #[Groups('user:write')]
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    #[Groups('user:write')]
    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    #[Groups('user:write')]
    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    #[Groups('user:write')]
    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getConversion(): ?string
    {
        return $this->conversion;
    }

    public function setConversion(string $conversion): static
    {
        $this->conversion = nl2br($conversion);

        return $this;
    }

    #[Groups('user:write')]
    #[SerializedName('conversion')]
    public function setTextConversion(string $conversion): static
    {
        $this->conversion = nl2br($conversion);

        return $this;
    }

    /**
     * Get the value of dateOfBirth
     */ 
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set the value of dateOfBirth
     *
     * @return  self
     */ 
    #[Groups('user:write')]
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    #[Groups('user:read')]
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    #[Groups('user:read')]
    public function getCreatedAtAgo()
    {
        return Carbon::parse($this->createdAt)->diffForHumans();
    }
}
