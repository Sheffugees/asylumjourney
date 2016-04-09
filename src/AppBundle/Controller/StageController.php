<?php

namespace AppBundle\Controller;

use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class StageController extends Controller
{

    /**
     * @Route("/stages", name="list_stages")
     */
    public function listStagesAction()
    {
        $normalizer = $this->normalizer();
        $stages = $this->getDoctrine()->getRepository("AppBundle:Stage")->findAll();

        $hal = new Hal('/stages', ['total' => count($stages)]);

        foreach ($stages as $stage) {
            $hal->addResource(
                'stages',
                (new Hal('/stages/' . $stage->getId()))->setData($normalizer->normalize($stage))
            );
        }

        return $this->halResponse($hal);
    }

    /**
     * @Route("/stages/{id}", name="read_stage")
     */
    public function readServiceAction($id)
    {
        $stage = $this->getDoctrine()->getRepository("AppBundle:Stage")->find($id);

        if (!$stage) {
            return new Response((new Hal(null, ['message' => 'Stage not found']))->addLink(
                'about',
                '/stages/' . $id
            )->asJson(true), 404, ['Content-Type' => 'application/vnd.error+json']);
        }

        return $this->halResponse(
            (new Hal('/stages/' . $stage->getId()))
                ->setData($this->normalizer()->normalize($stage))
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
