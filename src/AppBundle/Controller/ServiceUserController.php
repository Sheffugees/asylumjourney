<?php

namespace AppBundle\Controller;

use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ServiceUserController extends Controller
{

    /**
     * @Route("/service-users", name="list_service_users")
     */
    public function listServiceUsersAction()
    {
        $normalizer = $this->normalizer();
        $serviceUsers = $this->getDoctrine()->getRepository("AppBundle:ServiceUser")->findAll();

        $hal = new Hal('/service-users', ['total' => count($serviceUsers)]);

        foreach ($serviceUsers as $serviceUser) {
            $hal->addResource(
                'serviceUsers',
                (new Hal('/service-users/' . $serviceUser->getId()))->setData($normalizer->normalize($serviceUser))
            );
        }

        return $this->halResponse($hal);
    }

    /**
     * @Route("/service-users/{id}", name="read_service_user")
     */
    public function readServiceAction($id)
    {
        $serviceUser = $this->getDoctrine()->getRepository("AppBundle:ServiceUser")->find($id);

        if (!$serviceUser) {
            return new Response((new Hal(null, ['message' => 'Service User not found']))->addLink(
                'about',
                '/service-users/' . $id
            )->asJson(true), 404, ['Content-Type' => 'application/vnd.error+json']);
        }

        return $this->halResponse(
            (new Hal('/service-users/' . $serviceUser->getId()))
                ->setData($this->normalizer()->normalize($serviceUser))
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
