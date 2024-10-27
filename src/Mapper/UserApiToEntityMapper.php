<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\Entity\UserProfile;
use App\ApiResource\UserApi;
use App\Entity\Subscription;
use App\Repository\UserRepository;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsMapper(from: UserApi::class, to: User::class)]
class UserApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private MicroMapperInterface $microMapper,
        private PropertyAccessorInterface $propertyAccessor
    ){}

    public function load(object $from, string $toClass, array $context): object 
    {
        $dto = $from;
        // dd(['from' => $dto]);
        assert($dto instanceof UserApi);

        /** @var User $entity */
        $entity = $dto->id ? $this->userRepository->findOneBy(['id' => $dto->id]) : new User();

        // dd($entity);

        if(!$entity) {
            throw new \Exception('User not found!');
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        assert($dto instanceof UserApi);
        $entity = $to;
        assert($entity instanceof User);

        // dd(['dto' => $dto,'entity' => $entity]);
        // $entity->setId(22);
        $entity->setFirstName($dto->firstName);
        
        $entity->setLastName($dto->lastName);
        
        $entity->setEmail($dto->email);
        
        if($dto->password !== null && $entity->getPassword() !== $dto->password){
            $entity->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $entity, $dto->password
                    )
                );
        }
        
        $entity->setPhoneNumber($dto->phoneNumber);
        
        $entity->setGender($dto->gender);
        
        $entity->setLocation($dto->location);
        
        $entity->setDateOfBirth($dto->dateOfBirth);
        
        $entity->setConversion($dto->conversion);

        if($dto->libReactCity !== null && $dto->libReactState !== $entity->getLibReactState()){
            $entity->setLibReactState($dto->libReactState); //2612
        }

        if($dto->libReactState !== null  && $dto->libReactCity !== $entity->getLibReactCity()) {
            $entity->setLibReactCity($dto->libReactCity); // 77340
        }
        
        if($dto->roles){
            $entity->setRoles( $dto->roles);
        }
        
        $userProfile = $this->microMapper->map($dto->userProfile, UserProfile::class,[
            MicroMapperInterface::MAX_DEPTH => 0
        ]);
        $entity->setUserProfile($userProfile);

        $products = [];
        foreach($dto->products as $product){
            $products[] = $this->microMapper->map( $product, Product::class,[
                MicroMapperInterface::MAX_DEPTH => 0
            ]);
        }
        $this->propertyAccessor->setValue($entity, 'products', $products);

        $userAddresses = [];
        if(!empty($dto->userAddresses)){
            foreach($dto->userAddresses as $userAddress){
                $userAddresses[] = $this->microMapper->map($userAddress, UserAddress::class,[
                    MicroMapperInterface::MAX_DEPTH => 1
                ]);
            }
            $this->propertyAccessor->setValue($entity, 'userAddresses', $userAddresses);
        }

        $shopOrders = [];
        foreach($dto->shopOrders as $shopOrder){
            $shopOrders[] = $this->microMapper->map( $shopOrder, ShopOrder::class,[
                MicroMapperInterface::MAX_DEPTH => 0
            ]);
        }
        $this->propertyAccessor->setValue($entity, 'shopOrders', $shopOrders);
        
        $subscriptions = [];
        foreach($dto->subscriptions as $subscription){
            $subscription = $this->microMapper->map( $subscription, Subscription::class,[
                MicroMapperInterface::MAX_DEPTH => 0
            ]);
        }
        $this->propertyAccessor->setValue( $entity, 'subscriptions', $subscriptions);

        return $entity;
    }
}