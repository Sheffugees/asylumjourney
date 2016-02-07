<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $normalizer = new GetSetMethodNormalizer();

        $callback = function ($dateTime) {
            return $dateTime instanceof \DateTime
                ? $dateTime->format(\DateTime::ISO8601)
                : '';
        };

        $normalizer->setCallbacks(['launch' => $callback, 'expiry' => $callback]);

        $serializer = new Serializer([$normalizer], [new JsonEncoder()]);

        return new Response($serializer->serialize(
            $this->getDoctrine()->getRepository("AppBundle:Service")->findAll(),
            "json"
        ), 200, ["Content-Type: application/json"]);
    }
}
