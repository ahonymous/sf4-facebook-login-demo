<?php

namespace App\Controller;

use App\Service\Traits\FacebookManagerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    use FacebookManagerTrait;

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            'link' => $this->facebookManager->getFbLoginUrl(),
        ]);
    }

    /**
     * @Route("/facebook", name="facebook")
     */
    public function facebook(): Response
    {
        return $this->redirectToRoute('secured-area');
    }


    /**
     * @Route("/secured-area", name="secured-area")
     */
    public function securedArea(): Response
    {
        return $this->render('main/secured-area.html.twig');
    }

}
