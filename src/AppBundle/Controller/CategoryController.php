<?php

namespace AppBundle\Controller;

use Nocarrier\Hal;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class CategoryController extends Controller
{

    /**
     * @Route("/categories", name="list_categories")
     */
    public function listCategoriesAction()
    {
        $normalizer = $this->normalizer();
        $categories = $this->getDoctrine()->getRepository("AppBundle:Category")->findAll();

        $hal = new Hal('/categories', ['total' => count($categories)]);

        foreach ($categories as $category) {
            $hal->addResource(
                'categories',
                (new Hal('/categories/' . $category->getId()))->setData($normalizer->normalize($category))
            );
        }

        return $this->halResponse($hal);
    }

    /**
     * @Route("/categories/{id}", name="read_category")
     */
    public function readServiceAction($id)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($id);

        if (!$category) {
            return new Response((new Hal(null, ['message' => 'Category not found']))->addLink(
                'about',
                '/categories/' . $id
            )->asJson(true), 404, ['Content-Type' => 'application/vnd.error+json']);
        }

        return $this->halResponse(
            (new Hal('/categories/' . $category->getId()))
                ->setData($this->normalizer()->normalize($category))
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
