<?php

namespace App\Facebook;

use App\Exception\FacebookException;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class FacebookManager.
 */
class FacebookManager
{
    use LoggerAwareTrait;

    private const FB_SCOPE = ['email'];
    private const FB_FIELDS = ['email', 'name', 'first_name', 'last_name'];

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * FacebookManager constructor.
     *
     * @param Facebook        $facebook
     * @param RouterInterface $router
     * @param LoggerInterface $logger
     */
    public function __construct(Facebook $facebook, RouterInterface $router, LoggerInterface $logger)
    {
        $this->facebook = $facebook;
        $this->router = $router;
        $this->logger = $logger;
    }

    /**
     * @return string
     */
    public function getFbLoginUrl(): string
    {
        return $this->facebook->getRedirectLoginHelper()->getLoginUrl(
            $this->router->generate('facebook', [], UrlGeneratorInterface::ABSOLUTE_URL),
            self::FB_SCOPE
        );
    }

    /**
     * @param Request|null $request
     *
     * @return GraphUser
     *
     * @throws FacebookException
     */
    public function getFbUserData(?Request $request = null): GraphUser
    {
        $helper = $this->facebook->getRedirectLoginHelper();
        if ($request) {
            $helper->getPersistentDataHandler()->set('state', $request->query->get('state'));
        }

        try {
            return $this->facebook
                ->get('me?fields='.implode(',', self::FB_FIELDS), $helper->getAccessToken())
                ->getGraphUser();
        } catch (FacebookResponseException $e) {
            $this->logger->error('Graph returned an error: '.$e->getMessage(), ['error' => $e]);
        } catch (FacebookSDKException $e) {
            $this->logger->error('Facebook SDK returned an error: '.$e->getMessage(), ['error' => $e]);
        }

        throw new FacebookException();
    }
}
