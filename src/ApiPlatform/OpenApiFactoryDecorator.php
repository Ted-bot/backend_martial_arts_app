<?php

namespace App\ApiPlatform;
use ArrayObject;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model\SecurityScheme;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use ApiPlatform\OpenApi\Factory\OpenApiFactory;
// use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;

#[AsDecorator('api_platform.openapi.factory')]
class OpenApiFactoryDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated,
        // private OpenApiFactory $factory,
    )
    {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);   

        // $securitySchemes = $openApi->getComponents()->getSecuritySchemes() ?: new ArrayObject();
        // $securitySchemes['access_token'] = new SecurityScheme(
        //     type: 'http',
        //     scheme: 'bearer'
        //     // scheme: 'bearer'
        // );
        $components = $openApi->getComponents()->withSecuritySchemes(
            new ArrayObject(
                [
                    'access_token' => new SecurityScheme(
                        type:         'http',
                        description:  'Value for the JWT Authorization header parameter.',
                        scheme:       'bearer',
                        // bearerFormat: 'JWT'
                    )
                ]
            )
        );
        // dd(['openApi_headers' => $components]);
        // return $openApi;
        return $openApi->withComponents($components);
    }
}