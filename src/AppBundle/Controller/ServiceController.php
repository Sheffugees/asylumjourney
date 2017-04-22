<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Issue;
use AppBundle\Entity\Provider;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceUser;
use AppBundle\Entity\Stage;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ServiceController extends Controller
{

    /**
     * @Route("/services", name="list_services", methods={"GET"})
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
            WHERE s.hidden = 0
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
     * @Route("/services/{id}", name="read_service", methods={"GET"})
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

    /**
     * @Route("/services", name="create_service", methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function createServiceAction(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        $service = $this->transformRequestData($data);

        $errors = $this->get('validator')->validate($service);

        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($service);
        $em->flush();

        return new Response(null, Response::HTTP_CREATED, [
            'Content-Type' => 'application/json',
            'Location' => "/services/{$service->getId()}",
        ]);
    }

    private function transformRequestData(array $data): Service
    {
        $service = new Service(
            $data['name'],
            $data['description']
        );

        if (isset($data['dataMaintainer'])) {
            $service->setDataMaintainer($data['dataMaintainer']);
        }

        if (isset($data['endDate'])) {
            $service->setEndDate(new \DateTime($data['endDate']));
        }

        if (isset($data['hidden'])) {
            $service->setHidden((bool) $data['hidden']);
        }

        if (isset($data['providers']) && is_array($data['providers'])) {
            $service->setProviders($this->transformEntityCollectionFromIds($data['providers'], Provider::class));
        }

        if (isset($data['stages']) && is_array($data['stages'])) {
            $service->setStages($this->transformEntityCollectionFromIds($data['stages'], Stage::class));
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            $service->setCategories($this->transformEntityCollectionFromIds($data['categories'], Category::class));
        }

        if (isset($data['serviceUsers']) && is_array($data['serviceUsers'])) {
            $service->setServiceUsers($this->transformEntityCollectionFromIds($data['serviceUsers'], ServiceUser::class));
        }

        if (isset($data['issues']) && is_array($data['issues'])) {
            $service->setIssues($this->transformEntityCollectionFromIds($data['issues'], Issue::class));
        }

        return $service;
    }

    private function transformEntityCollectionFromIds(array $ids, string $class): Collection
    {
        $repository = $this->getDoctrine()->getManager()->getRepository($class);

        return new ArrayCollection($repository->findBy(['id' => $ids]));
    }

    private function validationErrorResponse(ConstraintViolationListInterface $errors): JsonResponse
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
        }

        return new JsonResponse(['errors' => $messages], Response::HTTP_BAD_REQUEST);
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
