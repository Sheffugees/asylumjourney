<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProviderAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text');
        $formMapper->add('description', 'textarea', ['required' => false]);
        $formMapper->add('phone', 'text', ['required' => false]);
        $formMapper->add('email', 'text', ['required' => false]);
        $formMapper->add('website', 'text', ['required' => false]);
        $formMapper->add('facebook', 'text', ['required' => false]);
        $formMapper->add('twitter', 'text', ['required' => false]);
        $formMapper->add('contactName', 'text', ['required' => false]);
        $formMapper->add('address', 'textarea', ['required' => false]);
        $formMapper->add('postcode', 'text', ['required' => false]);
        $formMapper->add('lastReviewDate', 'date', ['required' => false]);
        $formMapper->add('lastReviewedBy', 'text', ['required' => false]);
        $formMapper->add('lastReviewComments', 'textarea', ['required' => false, 'attr' => ['rows' => 15]]);
        $formMapper->add('nextReviewDate', 'date', ['required' => false]);
        $formMapper->add('nextReviewComments', 'textarea', ['required' => false, 'attr' => ['rows' => 15]]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('description')
            ->add('phone')
            ->add('email')
            ->add('website')
            ->add('facebook')
            ->add('twitter')
            ->add('contactName')
            ->add('lastReviewDate')
            ->add('lastReviewedBy')
            ->add('lastReviewComments')
            ->add('nextReviewComments')
            ->add('nextReviewDate');
    }
}