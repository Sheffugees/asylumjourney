<?php

namespace AppBundle\Controller;

use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class IssueController extends Controller
{

    /**
     * @Route("/issues", name="list_issues")
     */
    public function listIssuesAction()
    {
        $normalizer = $this->normalizer();
        $issues = $this->getDoctrine()->getRepository("AppBundle:Issue")->findAll();

        $hal = new Hal('/issues', ['total' => count($issues)]);

        foreach ($issues as $issue) {
            $hal->addResource(
                'issues',
                (new Hal('/issues/' . $issue->getId()))->setData($normalizer->normalize($issue))
            );
        }

        return $this->halResponse($hal);
    }

    /**
     * @Route("/issues/{id}", name="read_issue")
     */
    public function readServiceAction($id)
    {
        $issue = $this->getDoctrine()->getRepository("AppBundle:Issue")->find($id);

        if (!$issue) {
            return new Response((new Hal(null, ['message' => 'Issue not found']))->addLink(
                'about',
                '/issues/' . $id
            )->asJson(true), 404, ['Content-Type' => 'application/vnd.error+json']);
        }

        return $this->halResponse(
            (new Hal('/issues/' . $issue->getId()))
                ->setData($this->normalizer()->normalize($issue))
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
