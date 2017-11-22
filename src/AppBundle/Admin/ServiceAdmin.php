<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\CollectionType;

class ServiceAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text');
        $formMapper->add('hidden', 'checkbox', ['required' => false]);
        $formMapper->add('description', 'textarea', ['required' => false, 'attr' => ['rows' => 15]]);
        $formMapper->add('dataMaintainer', 'text', ['required' => false]);
        $formMapper->add('endDate', 'date', ['required' => false]);
        $formMapper->add('events', 'textarea', ['required' => false, 'attr' => ['rows' => 15]]);
        $formMapper->add('providers', 'sonata_type_model', ['multiple' => true, 'required' => false]);
        $formMapper->add('stages', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('categories', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('serviceUsers', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('issues', 'sonata_type_model', ['multiple' => true, 'required' => false]);
        $formMapper->add('resources', 'sonata_type_model', ['multiple' => true, 'required' => false]);
        $formMapper->add(
            'resources',
            CollectionType::class,
            ['by_reference' => false],
            [
                'edit' => 'inline',
                'inline' => 'table',
            ]
        );
        $formMapper->add('lastReviewDate', 'date', ['required' => false]);
        $formMapper->add('lastReviewedBy', 'text', ['required' => false]);
        $formMapper->add('lastReviewComments', 'textarea', ['required' => false, 'attr' => ['rows' => 15]]);
        $formMapper->add('nextReviewDate', 'date', ['required' => false]);
        $formMapper->add('nextReviewComments', 'textarea', ['required' => false, 'attr' => ['rows' => 15]]);
    }
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('hidden')
            ->add('description')
            ->add('providers')
            ->add('stages')
            ->add('categories');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
    }

    public function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('hidden')
            ->add('description')
            ->add('dataMaintainer')
            ->add('endDate')
            ->add('events')
            ->add('providers')
            ->add('stages')
            ->add('categories')             
            ->add('serviceUsers')             
            ->add('issues')
            ->add('resources')
            ->add('lastReviewDate')
            ->add('lastReviewedBy')
            ->add('lastReviewComments')
            ->add('nextReviewComments')
            ->add('nextReviewDate');
    }
}
