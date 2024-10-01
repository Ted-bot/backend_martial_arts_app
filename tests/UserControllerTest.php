<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserControllerTest extends KernelTestCase
{
    use HasBrowser;
    use ResetDatabase;
    public function testUpdateUser(): void
    {
        self::bootKernel();

        $user = UserFactory::createOne(['firstName' => 'John']);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/' . $user->getId(),[
                'json' => ['firstName' => 'Max'],
                'headers' => [
                    'Content-Type' => 'application/merge-patch+json'
                    ]
                ],
            )
            ->dump()
            ->assertStatus(200)
        ;
        
            // $token = self::getContainer()->get('lexik_jwt_authentication.encoder')
        // ->encode(['username' => 'tkay']);

        // dd( ['token' => $token]);
        // $this->browser()
        // ->post('/api/tokenmanager', [
        //     'json' => [], // <= set data
        //     'Authorization' => 'Bearer ' . $token
        // ])
    }
}
