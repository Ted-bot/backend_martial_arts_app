<?php

namespace App\Entity;

use DateTimeZone;
use Carbon\Carbon;
use App\Class\Role;
use DateTimeImmutable;
use App\Entity\Product;
use App\Entity\UserProfile;
use ApiPlatform\Metadata\Link;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Dto\User\CreateUserDto;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


// #[ApiResource(
//     shortName: 'User',
//     operations: [
//         new Get(    
//             uriTemplate: '/user_by_email/{email}/email',
//             uriVariables: 'email'
//         ),
//         new Patch(    
//             uriTemplate: '/user_by_email/{email}/email',
//             uriVariables: 'email'
//         )
//     ],
// )]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USER', fields: ['email', 'phone_number'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // #[ApiProperty(identifier: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // // #[ApiProperty(indentifier: true)]
    // #[Groups(['user:read', 'user:write','profile:read'])]
    /**
     * @var string Email of user
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    // #[Groups(['user:read', 'user:write','profile:read'])]
    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    // #[Groups(['user:read'])]
    #[ORM\OneToOne(mappedBy: 'userUniq', targetEntity: UserProfile::class,cascade: ['persist'])] //, 'remove'
    private ?UserProfile $userProfile = null;

    // #[Groups(['user:read', 'user:write','profile:read', 'subscription:read'])]
    /**
     * @var string Email of user
     */
    #[ORM\Column(length: 25)]
    private $firstName = null;

    // #[Groups(['user:read', 'user:write','profile:read'])]
    /**
     * @var string Lastname of user
     */
    #[ORM\Column(length: 25)]
    private ?string $lastName = null;

    // #[Groups(['user:read', 'user:write','profile:read'])]
    /**
     * @var string Phonenumber of user
     */
    #[ORM\Column(length: 20)]
    private ?string $phoneNumber = null;

    // #[Groups(['user:read', 'user:write','profile:read'])]
    /**
     * @var string A "d/m/Y" formatted value
     */
    #[ORM\Column(length: 10)]
    private ?string $dateOfBirth = null;
        
    // #[Groups(['user:read', 'user:write','profile:read'])]
    /**
     * @var string Gender of user
     */
    #[ORM\Column(length: 6)]
    private ?string $gender = null;

    // #[Groups(['user:read', 'user:write', 'profile:read'])]
    /**
     * @var string Location of user
     */
    #[ORM\Column(length: 50)]
    private ?string $location = null;

    // #[Groups(['user:read', 'user:write', 'profile:read'])]
    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
    /**
     * @var string Conversion of user
     */
    #[ORM\Column(length: 255)]
    protected ?string $conversion = null;

    // #[Groups(['user:read', 'user:write', 'profile:read'])]
    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
    /**
     * @var string datetime created account of user
     */
    #[ORM\Column(length: 25)]
    private ?DateTimeImmutable $createdAt;

    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
    /**
     * @var Collection<int, Product>
     */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'relatedUser')]
    private Collection $products;

    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
    /**
     * @var Collection<int, UserAddress>
     */
    #[ORM\OneToMany(targetEntity: UserAddress::class, mappedBy: 'addressUser', fetch: 'EAGER')]
    private Collection $userAddresses;

    /**
     * @var Collection<int, ShopOrder>
     */
    #[ORM\OneToMany(targetEntity: ShopOrder::class, mappedBy: 'orderOwnedBy')]
    private Collection $shopOrders;

    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
    #[ORM\Column(length: 4, nullable: true)]
    private ?string $libReactState = null;

    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
    #[ORM\Column(length: 5, nullable: true)]
    private ?string $libReactCity = null;

    // #[Groups(['user:read', 'subscription:read'])]
    /**
     * @var Collection<int, Subscription>
     */
    #[Link(toProperty: 'subscription')]
    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'subscriptionOwnedBy', fetch: 'LAZY')]
    public Collection $subscriptions;

    public function __construct()
    {
        $dateTime = new DateTimeImmutable();
        $this->createdAt = $dateTime->setTimezone(new DateTimeZone('Europe/Amsterdam'));
        $this->setRoles([Role::ROLE_USER_STUDENT]);
        // $this->roles = [Role::ROLE_USER_STUDENT];
        $this->products = new ArrayCollection();
        $this->userAddresses = new ArrayCollection();
        $this->shopOrders = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
    }

    public function createNewUserObj(CreateUserDto $user)
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
        // $this->setRoles([Role::ROLE_USER_STUDENT]);

        $this->setLibReactState($user->stateId);
        $this->setLibReactCity($user->cityId);

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

//     public function setId(int $id): void
// {
//     $this->id = $id;
// }

    public function getEmail(): ?string
    {
        return (string) $this->email;
    }

    // #[Groups('user:write')]
    public function setEmail(string $email): static
    {
        $this->email = (string) $email;

        return $this;
    }

    // #[ApiProperty(security: 'is_granted("Role_Admin")')]
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
        return array_unique($roles);
    }

    // #[Groups('user:write')]
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

    // #[Groups('user:write')]
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

    // #[Groups('user:write')]
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

    // #[Groups('user:write')]
    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    // #[Groups('user:write')]
    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    // #[Groups('user:write')]
    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    // #[Groups('user:write')]
    public function setGender(string $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    // #[Groups('user:write')]
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

    // #[Groups('user:write')]
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

    // #[Groups('user:write')]
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

    // #[Groups('user:read')]
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    // #[Groups('user:read')]
    public function getCreatedAtAgo()
    {
        return Carbon::parse($this->createdAt)->diffForHumans();
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setRelatedUser($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getRelatedUser() === $this) {
                $product->setRelatedUser(null);
            }
        }

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
            $userAddress->setAddressUser($this);
        }

        return $this;
    }

    public function removeUserAddress(UserAddress $userAddress): static
    {
        if ($this->userAddresses->removeElement($userAddress)) {
            // set the owning side to null (unless already changed)
            if ($userAddress->getAddressUser() === $this) {
                $userAddress->setAddressUser(null);
            }
        }

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
            $shopOrder->setOrderOwnedBy($this);
        }

        return $this;
    }

    public function removeShopOrder(ShopOrder $shopOrder): static
    {
        if ($this->shopOrders->removeElement($shopOrder)) {
            // set the owning side to null (unless already changed)
            if ($shopOrder->getOrderOwnedBy() === $this) {
                $shopOrder->setOrderOwnedBy(null);
            }
        }

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

    public function getLibReactCity(): ?string
    {
        return $this->libReactCity;
    }

    public function setLibReactCity(?string $libReactCity): static
    {
        $this->libReactCity = $libReactCity;

        return $this;
    }

    /**
     * @return Collection<int, Subscription>
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): static
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
            $subscription->setSubscriptionOwnedBy($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): static
    {
        if ($this->subscriptions->removeElement($subscription)) {
            // set the owning side to null (unless already changed)
            if ($subscription->getSubscriptionOwnedBy() === $this) {
                $subscription->setSubscriptionOwnedBy(null);
            }
        }

        return $this;
    }


}
