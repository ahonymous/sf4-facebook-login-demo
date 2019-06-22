<?php

namespace App\Service\Traits;

use App\Facebook\FacebookManager;

/**
 * Trait FacebookManager.
 */
trait FacebookManagerTrait
{
    /**
     * @var FacebookManager
     */
    private $facebookManager;

    /**
     * @param FacebookManager $facebookManager
     *
     * @required
     */
    public function setFacebookManager(FacebookManager $facebookManager): void
    {
        $this->facebookManager = $facebookManager;
    }
}
