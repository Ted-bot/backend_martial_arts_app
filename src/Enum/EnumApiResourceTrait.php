<?php

declare(strict_types=1);

namespace App\Enum;

use ApiPlatform\Metadata\Operation;
use Symfony\Component\Serializer\Annotation\Groups;

trait EnumApiResourceTrait
{
    public function getId()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function getCases()
    {
        return self::cases();
    }

    public static function getCase(Operation $operation, array $uriVariables)
    {
        $name = $uriVariables['id'] ?? null;
        return self::tryFrom($name);
    }
}