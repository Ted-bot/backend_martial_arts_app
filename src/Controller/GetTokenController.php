<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

final class GetTokenController
{
    public function __invoke(): Response
    {
        // return new Response('', Response::HTTP_NO_CONTENT);
        return new Response('GangGang', Response::HTTP_OK);
    }
}