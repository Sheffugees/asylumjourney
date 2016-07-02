<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ServiceAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text');
        $formMapper->add('description', 'textarea', ['required' => false, 'attr' => ['rows' => 15]]);
        $formMapper->add('dataMaintainer', 'text', ['required' => false]);
        $formMapper->add('endDate', 'date', ['required' => false]);
        $formMapper->add('providers', 'sonata_type_model', ['multiple' => true, 'required' => false]);
        $formMapper->add('stages', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('categories', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('serviceUsers', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('issues', 'sonata_type_model', ['multiple' => true, 'required' => false]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('description')
            ->add('providers')
            ->add('stages')
            ->add('categories');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
    }
}
