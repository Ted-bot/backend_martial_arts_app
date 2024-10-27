<?php

namespace App\Mapper;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\ShopOrder;
use App\Entity\UserAddress;
use App\ApiResource\UserApi;
use App\Entity\Subscription;
use App\ApiResource\ProductApi;
use App\ApiResource\ShopOrderApi;
use App\ApiResource\UserAddressApi;
use App\ApiResource\UserProfileApi;
use App\ApiResource\SubscriptionApi;
use Symfonycasts\MicroMapper\AsMapper;
use App\Repository\UserAddressRepository;
use Doctrine\Common\Collections\Criteria;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: User::class, to: UserApi::class)]
class UserEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private UserAddressRepository $uAddressRepo,
    ){}

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof User);

        $dto = new UserApi();
        $dto->id = $entity->getId();

        $userAddresses = $entity->getUserAddresses();

//         $criteria = Criteria::create()
//             ->where(Criteria::expr()->eq("isDefault", "true"))
//             // ->orderBy(array("username" => Criteria::ASC))
//             // ->setFirstResult(1)
//             // ->setMaxResults(1)
// ;
//         $defaultAddress = $userAddresses->matching($criteria);
        $defaultAddress = $this->uAddressRepo->findUserDefaultAddress($dto->id);
        // $defaultAddress = $userAddresses->matching($criteria);

        $dto->userAddress = $defaultAddress;

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        // dd(['from' => $entity,'to' => $dto]);
        assert($entity instanceof User);
        assert($dto instanceof UserApi);

        // $dto = new UserApi();
        $dto->id = $entity->getId();
        $dto->firstName = $entity->getFirstName();
        $dto->lastName = $entity->getLastName();
        $dto->email = $entity->getEmail();
        // $dto->password = $entity->getPassword();
        $dto->phoneNumber = $entity->getPhoneNumber();
        $dto->gender = $entity->getGender();
        $dto->dateOfBirth = $entity->getDateOfBirth();
        $dto->location = $entity->getLocation();
        $dto->conversion = $entity->getConversion();
        $dto->roles = $entity->getRoles();
        $dto->createdAt = $entity->getCreatedAt();
        $dto->libReactCity = $entity->getLibReactCity();
        $dto->libReactState = $entity->getLibReactState();
        
        // if($entity->getUserProfile()){
            $dto->userProfile = $this->microMapper->map($entity->getUserProfile(), UserProfileApi::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]); // $this->microMapper->map()
        // }
        
        // $dto->products = $entity->getProducts()->toArray();
        // if($entity->getUserProfile()){
            $dto->products = array_map(function(Product $product) {
                return $this->microMapper->map($product, ProductApi::class, [
                    MicroMapperInterface::MAX_DEPTH => 1
                ]); 
                },$entity->getProducts()->toArray()
            );
        // }

        // $dto->shopOrders = $entity->getShopOrders()->toArray();
        // if($entity->getShopOrders()?->toArray()){
            $dto->shopOrders = array_map(function(ShopOrder $shopOrder) {
                return $this->microMapper->map($shopOrder, ShopOrderApi::class, [
                    MicroMapperInterface::MAX_DEPTH => 1
                ]); 
                }, $entity->getShopOrders()->toArray()
            );
        // }

        // $dto->subscriptions = $entity->getSubscriptions()->toArray();
        // if($entity->getShopOrders()?->toArray()){
            $dto->subscriptions = array_map(function(Subscription $subscription) {
                return $this->microMapper->map($subscription, SubscriptionApi::class, [
                    MicroMapperInterface::MAX_DEPTH => 1
                ]); 
                }, $entity->getSubscriptions()->toArray()
            );
        // }

        $dto->userAddresses = array_map(function(UserAddress $userAddress) {
            return $this->microMapper->map($userAddress, UserAddressApi::class, [
                MicroMapperInterface::MAX_DEPTH => 0
            ]); 
            }, $entity->getUserAddresses()->toArray()
        );

        // dd(['dto' => $dto]);
        return $dto;
    }
}