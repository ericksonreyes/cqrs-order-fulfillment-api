<?php

namespace App\Services;

use EricksonReyes\DomainDrivenDesign\Infrastructure\IdentityGenerator;
use Exception;
use Ramsey\Uuid\Uuid;

class RamseyIdentityGenerator implements IdentityGenerator
{
    /**
     * @param string $prefix
     * @return string
     * @throws Exception
     */
    public function nextIdentity($prefix = ''): string
    {
        return $prefix . Uuid::uuid4()->toString();
    }
}
