<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ResourceLink;
use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ResourceController extends Controller
{

    /**
     * @Route("/resources", name="list_resources", methods={"GET", "HEAD"})
     */
    public function listResourcesAction()
    {
        $resources = $this->getDoctrine()->getRepository("AppBundle:ResourceLink")->findAll();

        $hal = new Hal('/resources', ['total' => count($resources)]);

        foreach ($resources as $resource) {
            $hal->addResource(
                'resources',
                (new Hal('/resources/' . $resource->getId()))->setData($this->getData($resource))
            );
        }

        return $this->halResponse($hal);
    }

    /**
     * @Route("/resources/{id}", name="read_resource", methods={"GET", "HEAD"})
     */
    public function readResourceAction($id)
    {
        $resource = $this->fetchResource($id);

        if (!$resource) {
            return $this->notFoundResponse($id);
        }

        return $this->halResponse(
            (new Hal('/resources/' . $resource->getId()))
                ->setData($this->getData($resource))
        );
    }

    /**
     * @Route("/resources", name="create_resource", methods={"POST"})
     */
    public function createResourceAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $parametersAsArray = $this->parametersFromJson($request);

        $name = isset ($parametersAsArray['name']) ? $parametersAsArray['name'] : null;
        $url = isset ($parametersAsArray['url']) ? $parametersAsArray['url'] : null;
        $expiryDate = isset ($parametersAsArray['expiryDate']) ? new \DateTime($parametersAsArray['expiryDate']) : null;
        $comments = isset ($parametersAsArray['comments']) ? $parametersAsArray['comments'] : null;

        $resource = new ResourceLink();
        $resource->setName($name);
        $resource->setUrl($url);
        $resource->setExpiryDate($expiryDate);
        $resource->setComments($comments);

        $errors = $this->get('validator')->validate($resource);

        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($resource);
        $entityManager->flush();

        return new Response(
            null,
            Response::HTTP_CREATED,
            ['location' => '/resources/' . $resource->getId(), 'Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/resources/{id}", name="edit_resource", methods={"PUT"})
     */
    public function editResourceAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $resource = $this->fetchResource($id);

        if (!$resource) {
            return $this->notFoundResponse($id);
        }

        $parametersAsArray = $this->parametersFromJson($request);

        $name = isset ($parametersAsArray['name']) ? $parametersAsArray['name'] : null;
        $url = isset ($parametersAsArray['url']) ? $parametersAsArray['url'] : null;
        $expiryDate = isset ($parametersAsArray['expiryDate']) ? new \DateTime($parametersAsArray['expiryDate']) : null;
        $comments = isset ($parametersAsArray['comments']) ? $parametersAsArray['comments'] : null;

        $resource = new ResourceLink();
        $resource->setName($name);
        $resource->setUrl($url);
        $resource->setExpiryDate($expiryDate);
        $resource->setComments($comments);

        $errors = $this->get('validator')->validate($resource);

        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        return new Response(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    /**
     * @Route("/resources/{id}", name="delete_resource", methods={"DELETE"})
     */
    public function deleteResourceAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $resource = $this->fetchResource($id);

        if (!$resource) {
            return $this->notFoundResponse($id);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($resource);
        $entityManager->flush();

        return new Response(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    private function halResponse(Hal $resource)
    {
        return new Response($resource->asJson(true), 200, ['Content-Type' => 'application/hal+json']);
    }

    private function fetchResource($id)
    {
        return $this->getDoctrine()->getRepository("AppBundle:ResourceLink")->find($id);
    }

    private function parametersFromJson(Request $request)
    {
        if ($content = $request->getContent()) {
            return json_decode($content, true);
        }
        return [];
    }

    private function notFoundResponse($id)
    {
        return new Response(
            (new Hal(null, ['message' => 'Resource not found']))->addLink(
                'about',
                '/resources/' . $id
            )->asJson(true), 404, ['Content-Type' => 'application/vnd.error+json']
        );
    }

    /**
     * @param $errors
     * @return Response
     */
    private function validationErrorResponse($errors)
    {
        $messages = [];
        foreach ($errors as $error) {
            $messages[$error->getPropertyPath()] = $error->getMessage();
        }

        return new Response(
            (new Hal(null, ['errors' => $messages]))->addLink(
                'about',
                '/resources'
            )->asJson(true), 401, ['Content-Type' => 'application/vnd.error+json']
        );
    }

    private function getData(ResourceLink $resource)
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
