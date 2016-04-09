<?php

namespace AppBundle\Controller;

use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->halResponse(
            (new Hal('/'))
                ->addLink('services', '/services')
                ->addLink('providers', '/providers')
                ->addLink('stages', '/stages')
                ->addLink('issues', '/issues')
                ->addLink('categories', '/categories')
                ->addLink('serviceUsers', '/service-users')
        );
    }

    private function halResponse(Hal $resource)
    {
        return new Response($resource->asJson(true), 200, ['Content-Type' => 'application/hal+json']);
    }
}
