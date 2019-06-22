<?php

namespace App\Service\Traits;

use Symfony\Component\Routing\RouterInterface;

/**
 * Trait RouterTrait.
 */
trait RouterTrait
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     *
     * @required
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }
}
