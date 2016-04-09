<?php

namespace AppBundle\Controller;

use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ProviderController extends Controller
{

    /**
     * @Route("/providers", name="list_providers")
     */
    public function listProvidersAction()
    {
        $normalizer = $this->normalizer();
        $providers = $this->getDoctrine()->getRepository("AppBundle:Provider")->findAll();

        $hal = new Hal('/providers', ['total' => count($providers)]);

        foreach ($providers as $provider) {
            $hal->addResource(
                'providers',
                (new Hal('/providers/' . $provider->getId()))->setData($normalizer->normalize($provider))
            );
        }

        return $this->halResponse($hal);
    }

    /**
     * @Route("/providers/{id}", name="read_provider")
     */
    public function readServiceAction($id)
    {
        $provider = $this->getDoctrine()->getRepository("AppBundle:Provider")->find($id);

        if (!$provider) {
            return new Response((new Hal(null, ['message' => 'Provider not found']))->addLink(
                'about',
                '/providers/' . $id
            )->asJson(true), 404, ['Content-Type' => 'application/vnd.error+json']);
        }

        return $this->halResponse(
            (new Hal('/providers/' . $provider->getId()))
                ->setData($this->normalizer()->normalize($provider))
        );
    }

    private function normalizer()
    {
        return (new GetSetMethodNormalizer());
    }

    private function halResponse(Hal $resource)
    {
        return new Response($resource->asJson(true), 200, ['Content-Type' => 'application/hal+json']);
    }
}
