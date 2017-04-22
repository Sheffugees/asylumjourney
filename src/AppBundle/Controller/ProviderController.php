<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Provider;
use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ProviderController extends Controller
{

    /**
     * @Route("/providers", name="list_providers", methods={"GET", "HEAD"})
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
    public function readProviderAction($id)
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

    /**
     * @Route("/providers/", name="create_provider", methods={"POST"})
     */
    public function createProviderAction(Request $request)
    {
        $parametersAsArray = [];
        if ($content = $request->getContent()) {
            $parametersAsArray = json_decode($content, true);
        }

        $name = $parametersAsArray['name']; //return error
        $description = isset ($parametersAsArray['description']) ? $parametersAsArray['description'] : null;
        $phoneNumber = isset ($parametersAsArray['description']) ? $parametersAsArray['phoneNumber'] : null;
        $email = isset ($parametersAsArray['email']) ? $parametersAsArray['email'] : null;
        $website = isset ($parametersAsArray['website']) ? $parametersAsArray['website'] : null;
        $contactName = isset ($parametersAsArray['contactName']) ? $parametersAsArray['contactName'] : null;
        $address = isset ($parametersAsArray['addresss']) ? $parametersAsArray['addresss'] : null;
        $postcode = isset ($parametersAsArray['postcode']) ? $parametersAsArray['postcode'] : null;

        $provider = new Provider($name, $description, $phoneNumber, $email, $website, $contactName, $address, $postcode);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($provider);
        $entityManager->flush();

        return new JsonResponse($parametersAsArray);
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
