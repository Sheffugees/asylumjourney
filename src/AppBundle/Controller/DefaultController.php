<?php

namespace AppBundle\Controller;

use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="home")
     */
    public function indexAction()
    {
        return $this->halResponse((new Hal('/'))->addLink('services', '/services'));
    }

    /**
     * @Route("/services", name="list_services")
     */
    public function listServicesAction()
    {
        $normalizer = $this->normalizer();
        $services = $this->getDoctrine()->getRepository("AppBundle:Service")->findAll();

        $hal = new Hal('/services', ['total' => count($services)]);

        foreach ($services as $service) {
            $hal->addResource(
                'services',
                (new Hal('/services/' . $service->getId()))->setData($normalizer->normalize($service))
            );
        }

        return $this->halResponse($hal);
    }

    /**
     * @Route("/services/{id}", name="read_service")
     */
    public function readServiceAction($id)
    {
        $service = $this->getDoctrine()->getRepository("AppBundle:Service")->find($id);

        if (!$service) {
            return new Response((new Hal(null, ['message' => 'Service not found']))->addLink(
                'about',
                '/services/' . $id
            )->asJson(true), 404, ['Content-Type' => 'application/vnd.error+json']);
        }

        return $this->halResponse(
            (new Hal('/services/' . $service->getId()))
                ->setData($this->normalizer()->normalize($service))
        );
    }

    private function normalizer()
    {
        $callback = function ($dateTime) {
            return $dateTime instanceof \DateTime
                ? $dateTime->format(\DateTime::ISO8601)
                : '';
        };

        return (new GetSetMethodNormalizer())->setCallbacks(['launch' => $callback, 'expiry' => $callback]);
    }

    private function halResponse(Hal $resource)
    {
        return new Response($resource->asJson(true), 200, ['Content-Type' => 'application/hal+json']);
    }
}
