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
        $formMapper->add('description', 'textarea');
        $formMapper->add('dataMaintainer', 'text');
        $formMapper->add('endDate', 'date');
        $formMapper->add('providers', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('stages', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('categories', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('serviceUsers', 'sonata_type_model', ['multiple' => true]);
        $formMapper->add('issues', 'sonata_type_model', ['multiple' => true]);
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name');
    }
}