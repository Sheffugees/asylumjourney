<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Provider;
use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProviderController extends Controller
{

    /**
     * @Route("/providers", name="list_providers", methods={"GET", "HEAD"})
     */
    public function listProvidersAction()
    {
        $providers = $this->getDoctrine()->getRepository("AppBundle:Provider")->findAll();

        $hal = new Hal('/providers', ['total' => count($providers)]);

        foreach ($providers as $provider) {
            $hal->addResource(
                'providers',
                (new Hal('/providers/' . $provider->getId()))->setData($this->getData($provider))
            );
        }

        return $this->halResponse($hal);
    }

    /**
     * @Route("/providers/{id}", name="read_provider", methods={"GET", "HEAD"})
     */
    public function readProviderAction($id)
    {
        $provider = $this->fetchProvider($id);

        if (!$provider) {
            return $this->notFoundResponse($id);
        }

        return $this->halResponse(
            (new Hal('/providers/' . $provider->getId()))
                ->setData($this->getData($provider))
        );
    }

    /**
     * @Route("/providers", name="create_provider", methods={"POST"})
     */
    public function createProviderAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $parametersAsArray = $this->parametersFromJson($request);

        $name = isset ($parametersAsArray['name']) ? $parametersAsArray['name'] : null;
        $description = isset ($parametersAsArray['description']) ? $parametersAsArray['description'] : null;
        $phoneNumber = isset ($parametersAsArray['phone']) ? $parametersAsArray['phone'] : null;
        $email = isset ($parametersAsArray['email']) ? $parametersAsArray['email'] : null;
        $website = isset ($parametersAsArray['website']) ? $parametersAsArray['website'] : null;
        $facebook = isset ($parametersAsArray['facebook']) ? $parametersAsArray['facebook'] : null;
        $twitter = isset ($parametersAsArray['twitter']) ? $parametersAsArray['twitter'] : null;
        $contactName = isset ($parametersAsArray['contactName']) ? $parametersAsArray['contactName'] : null;
        $address = isset ($parametersAsArray['address']) ? $parametersAsArray['address'] : null;
        $postcode = isset ($parametersAsArray['postcode']) ? $parametersAsArray['postcode'] : null;
        $lastReviewDate = isset ($parametersAsArray['lastReviewDate']) ? new \DateTime($parametersAsArray['lastReviewDate']) : null;
        $lastReviewedBy = isset ($parametersAsArray['lastReviewedBy']) ? $parametersAsArray['lastReviewedBy'] : null;
        $lastReviewComments = isset ($parametersAsArray['lastReviewComments']) ? $parametersAsArray['lastReviewComments'] : null;
        $nextReviewComments = isset ($parametersAsArray['nextReviewComments']) ? $parametersAsArray['nextReviewComments'] : null;
        $nextReviewDate = isset ($parametersAsArray['nextReviewDate']) ? new \DateTime($parametersAsArray['nextReviewDate']) : null;
        $providerContact = isset ($parametersAsArray['providerContact']) ? $parametersAsArray['providerContact'] : null;

        $provider = new Provider();
        $provider->setName($name);
        $provider->setDescription($description);
        $provider->setPhone($phoneNumber);
        $provider->setEmail($email);
        $provider->setWebsite($website);
        $provider->setFacebook($facebook);
        $provider->setTwitter($twitter);
        $provider->setContactName($contactName);
        $provider->setAddress($address);
        $provider->setPostcode($postcode);
        $provider->setLastReviewDate($lastReviewDate);
        $provider->setLastReviewedBy($lastReviewedBy);
        $provider->setLastReviewComments($lastReviewComments);
        $provider->setNextReviewDate($nextReviewDate);
        $provider->setNextReviewComments($nextReviewComments);
        $provider->setProviderContact($providerContact);

        $errors = $this->get('validator')->validate($provider);

        if (count($errors) > 0) {
            return $this->validationErrorResponse($errors);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($provider);
        $entityManager->flush();

        return new Response(
            null,
            Response::HTTP_CREATED,
            ['location' => '/providers/' . $provider->getId(), 'Content-Type' => 'application/json']
        );
    }

    /**
     * @Route("/providers/{id}", name="edit_provider", methods={"PUT"})
     */
    public function editProviderAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $provider = $this->fetchProvider($id);

        if (!$provider) {
            return $this->notFoundResponse($id);
        }

        $parametersAsArray = $this->parametersFromJson($request);

        $name = isset ($parametersAsArray['name']) ? $parametersAsArray['name'] : null;
        $description = isset ($parametersAsArray['description']) ? $parametersAsArray['description'] : null;
        $phoneNumber = isset ($parametersAsArray['phone']) ? $parametersAsArray['phone'] : null;
        $email = isset ($parametersAsArray['email']) ? $parametersAsArray['email'] : null;
        $website = isset ($parametersAsArray['website']) ? $parametersAsArray['website'] : null;
        $facebook = isset ($parametersAsArray['facebook']) ? $parametersAsArray['facebook'] : null;
        $twitter = isset ($parametersAsArray['twitter']) ? $parametersAsArray['twitter'] : null;
        $contactName = isset ($parametersAsArray['contactName']) ? $parametersAsArray['contactName'] : null;
        $address = isset ($parametersAsArray['address']) ? $parametersAsArray['address'] : null;
        $postcode = isset ($parametersAsArray['postcode']) ? $parametersAsArray['postcode'] : null;
        $lastReviewDate = isset ($parametersAsArray['lastReviewDate']) ? new \DateTime($parametersAsArray['lastReviewDate']) : null;
        $lastReviewedBy = isset ($parametersAsArray['lastReviewedBy']) ? $parametersAsArray['lastReviewedBy'] : null;
        $lastReviewComments = isset ($parametersAsArray['lastReviewComments']) ? $parametersAsArray['lastReviewComments'] : null;
        $nextReviewComments = isset ($parametersAsArray['nextReviewComments']) ? $parametersAsArray['nextReviewComments'] : null;
        $nextReviewDate = isset ($parametersAsArray['nextReviewDate']) ? new \DateTime($parametersAsArray['nextReviewDate']) : null;
        $providerContact = isset ($parametersAsArray['providerContact']) ? $parametersAsArray['providerContact'] : null;

        $provider->setName($name);
        $provider->setDescription($description);
        $provider->setPhone($phoneNumber);
        $provider->setEmail($email);
        $provider->setWebsite($website);
        $provider->setFacebook($facebook);
        $provider->setTwitter($twitter);
        $provider->setContactName($contactName);
        $provider->setAddress($address);
        $provider->setPostcode($postcode);
        $provider->setLastReviewDate($lastReviewDate);
        $provider->setLastReviewedBy($lastReviewedBy);
        $provider->setLastReviewComments($lastReviewComments);
        $provider->setNextReviewDate($nextReviewDate);
        $provider->setNextReviewComments($nextReviewComments);
        $provider->setProviderContact($providerContact);

        $errors = $this->get('validator')->validate($provider);

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
     * @Route("/providers/{id}", name="delete_provider", methods={"DELETE"})
     */
    public function deleteProviderAction($id)
    {
        $this->denyAccessUnlessGranted('ROLE_EDITOR');
        $provider = $this->fetchProvider($id);

        if (!$provider) {
            return $this->notFoundResponse($id);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($provider);
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

    /**
     * @param $id
     * @return Provider
     */
    private function fetchProvider($id)
    {
        return $this->getDoctrine()->getRepository("AppBundle:Provider")->find($id);
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    private function parametersFromJson(Request $request)
    {
        if ($content = $request->getContent()) {
            return json_decode($content, true);
        }
        return [];
    }

    /**
     * @param $id
     * @return Response
     */
    private function notFoundResponse($id)
    {
        return new Response(
            (new Hal(null, ['message' => 'Provider not found']))->addLink(
                'about',
                '/providers/' . $id
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
                '/providers'
            )->asJson(true), 401, ['Content-Type' => 'application/vnd.error+json']
        );
    }

    private function getData(Provider $provider)
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
            'providerContact' => $provider->getProviderContact(),
        ];
    }
}
