<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Service;
use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class ServiceController extends Controller
{

    /**
     * @Route("/services", name="list_services")
     */
    public function listServicesAction()
    {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('
            SELECT s, p, st, c, su, i
            FROM AppBundle:Service s
            LEFT JOIN s.providers p
            LEFT JOIN s.stages st
            LEFT JOIN s.categories c
            LEFT JOIN s.serviceUsers su
            LEFT JOIN s.issues i
        ');
        $services = $query->getResult();

        $hal = new Hal('/services', ['total' => count($services)]);

        foreach ($services as $service) {
            $hal->addResource(
                'services',
                $this->createServiceHal($service)
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

        return $this->halResponse($this->createServiceHal($service));
    }

    private function getData(Service $service)
    {
        return [
            'id' => $service->getId(),
            'name' => $service->getName(),
            'description' => $service->getDescription(),
            'dataMaintainer' => $service->getDataMaintainer(),
            'endDate' => $service->getISO8601EndDate()
        ];
    }

    private function halResponse(Hal $resource)
    {
        return new Response($resource->asJson(true), 200, ['Content-Type' => 'application/hal+json']);
    }

    private function createServiceHal(Service $service)
    {
        $normalizer = $this->normalizer();

        $hal = (new Hal('/services/' . $service->getId()))->setData($this->getData($service));

        foreach ($service->getProviders() as $provider) {
            $hal->addResource(
                'providers',
                (new Hal('/providers/' . $provider->getId()))->setData($normalizer->normalize($provider))
            );
        }

        foreach ($service->getStages() as $stage) {
            $hal->addResource(
                'stages',
                (new Hal('/stages/' . $stage->getId()))->setData($normalizer->normalize($stage))
            );
        }

        foreach ($service->getCategories() as $category) {
            $hal->addResource(
                'categories',
                (new Hal('/categories/' . $category->getId()))->setData($normalizer->normalize($category))
            );
        }

        foreach ($service->getServiceUsers() as $serviceUser) {
            $hal->addResource(
                'serviceUsers',
                (new Hal('/service-users/' . $serviceUser->getId()))->setData($normalizer->normalize($serviceUser))
            );
        }

        foreach ($service->getIssues() as $issue) {
            $hal->addResource(
                'issues',
                (new Hal('/issues/' . $issue->getId()))->setData($normalizer->normalize($issue))
            );
        }

        return $hal;
    }

    private function normalizer()
    {
        return (new GetSetMethodNormalizer());
    }
}
