<?php

namespace App\Entity;

use App\Class\Roles;
use App\Entity\UserProfile;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Request\CreateUserRequest;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USER', fields: ['email', 'phone_number'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var string Email of person
     */
    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'userUniq', targetEntity: UserProfile::class,cascade: ['persist', 'remove'])]
    private ?UserProfile $userProfile = null;

    /**
     * @var string Email of person
     */
    #[ORM\Column(length: 25)]
    private ?string $firstName = null;

    /**
     * @var string Lastname of person
     */
    #[ORM\Column(length: 25)]
    private ?string $lastName = null;

    /**
     * @var string Phonenumber of person
     */
    #[ORM\Column(length: 15)]
    private ?string $phoneNumber = null;

    /**
     * @var string A "Y-m-d H:i:s" formatted value
     */
    #[ORM\Column(length: 10)]
    private ?string $dateOfBirth = null;
        
    /**
     * @var string Gender of person
     */
    #[ORM\Column(length: 6)]
    private ?string $gender = null;

    /**
     * @var string Location of person
     */
    #[ORM\Column(length: 50)]
    private ?string $location = null;

    /**
     * @var string Conversion of person
     */
    #[ORM\Column(length: 255)]
    private ?string $conversion = null;

    /**
     * @var Collection<int, AccessToken>
     */
    #[ORM\OneToMany(targetEntity: AccessToken::class, mappedBy: 'owendBy')]
    private Collection $accessTokens;

    /** 
     * @var string data of person
     */
    private ?array $newUserObject = null;

    public function __construct()
    {
        $this->accessTokens = new ArrayCollection();
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
        $this->setRoles([Roles::ROLE_USER_STUDENT]);

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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
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

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

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
        $this->conversion = $conversion;

        return $this;
    }

    /**
     * @return Collection<int, AccessToken>
     */
    public function getAccessTokens(): Collection
    {
        return $this->accessTokens;
    }

    public function addAccessToken(AccessToken $accessToken): static
    {
        if (!$this->accessTokens->contains($accessToken)) {
            $this->accessTokens->add($accessToken);
            $accessToken->setOwendBy($this);
        }

        return $this;
    }

    public function removeAccessToken(AccessToken $accessToken): static
    {
        if ($this->accessTokens->removeElement($accessToken)) {
            // set the owning side to null (unless already changed)
            if ($accessToken->getOwendBy() === $this) {
                $accessToken->setOwendBy(null);
            }
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getValidTokenStrings(): array
    {
        return $this->getAccessTokens()
            ->filter(fn (AccessToken $token) => $token->isValid())
            ->map(fn (AccessToken $token) => $token->getToken())
            ->toArray()
        ;
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
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }
}
