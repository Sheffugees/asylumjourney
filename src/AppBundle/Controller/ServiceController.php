<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Issue;
use AppBundle\Entity\Provider;
use AppBundle\Entity\ResourceLink;
use AppBundle\Entity\Service;
use AppBundle\Entity\ServiceUser;
use AppBundle\Entity\Stage;
use DateTime;
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
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $data = json_decode($request->getContent(), true);

        $service = $this->mapDataToService($data);

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

    /**
     * @Route("/services/{id}", name="edit_service", methods={"PUT"})
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function editServiceAction(int $id, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $data = json_decode($request->getContent(), true);
        $service = $this->getDoctrine()->getRepository(Service::class)->find($id);

        if (!$service) {
            return $this->notFoundResponse($id);
        }

        $service = $this->mapDataToService($data, $service);

        $errors = $this->get('validator')->validate($service);

        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($service);
        $em->flush();

        return new Response(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @Route("/services/{id}", name="delete_service", methods={"DELETE"})
     *
     * @param int $id
     * @return Response
     */
    public function deleteServiceAction(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $service = $this->getDoctrine()->getRepository(Service::class)->find($id);

        if (!$service) {
            return $this->notFoundResponse($id);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($service);
        $em->flush();

        return new Response(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    private function mapDataToService(array $data, Service $service = null): Service
    {
        if (!$service) {
            $service = new Service();
        }

        if (isset($data['name'])) {
            $service->setName($data['name']);
        }

        if (isset($data['description'])) {
            $service->setDescription($data['description']);
        }

        if (isset($data['dataMaintainer'])) {
            $service->setDataMaintainer($data['dataMaintainer']);
        }

        if (isset($data['endDate'])) {
            $service->setEndDate(new DateTime($data['endDate']));
        }

        if (isset($data['hidden'])) {
            $service->setHidden((bool) $data['hidden']);
        }

        if (isset($data['events'])) {
            $service->setEvents($data['events']);
        }

        if (isset($data['lastReviewDate'])) {
            $service->setLastReviewDate(new DateTime($data['lastReviewDate']));
        }

        if (isset($data['nextReviewDate'])) {
            $service->setNextReviewDate(new DateTime($data['nextReviewDate']));
        }

        if (isset($data['lastReviewedBy'])) {
            $service->setLastReviewedBy($data['lastReviewedBy']);
        }

        if (isset($data['lastReviewComments'])) {
            $service->setLastReviewComments($data['lastReviewComments']);
        }

        if (isset($data['nextReviewComments'])) {
            $service->setNextReviewComments($data['nextReviewComments']);
        }

        if (isset($data['providers']) && is_array($data['providers'])) {
            $service->setProviders($this->mapEntityCollectionFromIds($data['providers'], Provider::class));
        }

        if (isset($data['stages']) && is_array($data['stages'])) {
            $service->setStages($this->mapEntityCollectionFromIds($data['stages'], Stage::class));
        }

        if (isset($data['categories']) && is_array($data['categories'])) {
            $service->setCategories($this->mapEntityCollectionFromIds($data['categories'], Category::class));
        }

        if (isset($data['serviceUsers']) && is_array($data['serviceUsers'])) {
            $service->setServiceUsers($this->mapEntityCollectionFromIds($data['serviceUsers'], ServiceUser::class));
        }

        if (isset($data['issues']) && is_array($data['issues'])) {
            $service->setIssues($this->mapEntityCollectionFromIds($data['issues'], Issue::class));
        }

        if (isset($data['resources']) && is_array($data['resources'])) {
            $service->setResources($this->mapEntityCollectionFromIds($data['resources'], ResourceLink::class));
        }

        if (isset($data['externalReviews'])) {
            $service->setExternalReviews($data['externalReviews']);
        }

        return $service;
    }

    private function mapEntityCollectionFromIds(array $ids, string $class): Collection
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

    /**
     * @param $id
     * @return Response
     */
    private function notFoundResponse(int $id): Response
    {
        return new Response(
            (new Hal(null, ['message' => 'Service not found']))->addLink(
                'about',
                '/services/' . $id
            )->asJson(true), 404, ['Content-Type' => 'application/vnd.error+json']
        );
    }

    private function getData(Service $service)
    {
        return [
            'id' => $service->getId(),
            'name' => $service->getName(),
            'description' => $service->getDescription(),
            'dataMaintainer' => $service->getDataMaintainer(),
            'endDate' => $service->getISO8601EndDate(),
            'events' => $service->getEvents(),
            'hidden' => $service->getHidden(),
            'lastReviewDate' => $service->getISO8601LastReviewDate(),
            'lastReviewedBy' => $service->getLastReviewedBy(),
            'lastReviewComments' => $service->getLastReviewComments(),
            'nextReviewDate' => $service->getISO8601NextReviewDate(),
            'nextReviewComments' => $service->getNextReviewComments(),
            'externalReviews' => $service->getExternalReviews(),
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
                (new Hal('/providers/' . $provider->getId()))->setData($this->getProviderData($provider))
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

        foreach ($service->getResources() as $resource) {
            $hal->addResource(
                'resources',
                (new Hal('/resources/' . $resource->getId()))->setData($this->getResourceData($resource))
            );
        }

        return $hal;
    }

    private function normalizer()
    {
        return (new GetSetMethodNormalizer());
    }

    private function getProviderData(Provider $provider)
    {
        return [
            'id' => $provider->getId(),
            'name' => $provider->getName(),
            'description' => $provider->getDescription(),
            'phone' => $provider->getPhone(),
            'email' => $provider->getEmail(),
            'website' => $provider->getWebsite(),
            'facebook' => $provider->getFacebook(),
            'twitter' => $provider->getTwitter(),
            'contactName' => $provider->getContactName(),
            'postcode' => $provider->getPostcode(),
            'address' => $provider->getAddress(),
            'lastReviewDate' => $provider->getISO8601LastReviewDate(),
            'lastReviewedBy' => $provider->getLastReviewedBy(),
            'lastReviewComments' => $provider->getLastReviewComments(),
            'nextReviewDate' => $provider->getISO8601NextReviewDate(),
            'nextReviewComments' => $provider->getNextReviewComments(),
            'providerContact' => $provider->getProviderContact()
        ];
    }

    private function getResourceData(ResourceLink $resource)
    {
        return [
            'id' => $resource->getId(),
            'name' => $resource->getName(),
            'url' => $resource->getUrl(),
            'expiryDate' => $resource->getISO8601ExpiryDate(),
            'comments' => $resource->getComments(),
        ];
    }
}
