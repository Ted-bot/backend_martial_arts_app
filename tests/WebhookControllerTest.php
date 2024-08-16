<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
// use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebhookControllerTest extends ApiTestCase
{

    use ResetDatabase;
    use Factories;

    // use Refres
    // public function testSomething(): void
    // {
    //     $client = static::createClient();
    //     $crawler = $client->request('GET', '/');

    //     $this->assertResponseIsSuccessful();
    //     $this->assertSelectorTextContains('h1', 'Hello World');
    // }

    public function testLogin(): void
    {
        $client = static::createClient();
        
        $data = [ 
            'json' => ['username' => 'tkbotch@gmail.com', 'password' => 'test_pass']
        ];
        
        $client->request(
            'POST',
            '/api/login_check', 
            $data,
        );

        // dd(['repsonseTest' => $client->getResponse()->getKernelResponse()]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

    public function testWebhook(): void
    {

        // dd($_ENV['APP_ENV']);
        // $client = static::createClient([
        //     'environment' => 'test',
        //     'debug'       => false,
        // ]);
        
        $client = static::createClient();
        
        // $data = ['status' => 'paid', 'order_id' => 'test']; // invalid
        $data = ['json' => ['id' => 'tr_DBPsz4sq7M']];
        // $this->browser()
        // ->post('api/mollie_direct_payments');
        $client->request(
            'POST', 
            '/api/webhook/MollieDirectPayment',
            $data,
        );

        // $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // $backend = static::findIriBy('/webhook/mollie_direct_payment', []);

        // $this->assertIsString($backend);
        // $client->request(
        //     'POST',
        //     '/api/v1/login',
        //     [],
        //     [],
        //     ['HTTP_HOST' => 'localhost:80'],
        //     // $data,
        //     json_encode($data),
        // );
        // $client->request('GET', '/api/products');
        dd(['repsonseTest' => $client->getResponse()->getKernelResponse()]);
        // dd(['responseTest' => $client->getResponse()->toArray()]);
        // $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }
}
