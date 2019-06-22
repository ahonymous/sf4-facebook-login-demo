<?php

namespace App\Service\Traits;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Trait DoctrineTrait.
 */
trait DoctrineTrait
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @param RegistryInterface $doctrine
     *
     * @required
     */
    public function setDoctrine(RegistryInterface $doctrine): void
    {
        $this->doctrine = $doctrine;
    }
}
