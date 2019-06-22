<?php

namespace App\Service\Traits;

use Psr\Log\LoggerInterface;

/**
 * Trait LoggerTrait.
 */
trait LoggerTrait
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     *
     * @required
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
