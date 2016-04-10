<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ProviderAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text');
        $formMapper->add('description', 'textarea', ['required' => false]);
        $formMapper->add('phone', 'text', ['required' => false]);
        $formMapper->add('email', 'text', ['required' => false]);
        $formMapper->add('website', 'text', ['required' => false]);
        $formMapper->add('contactName', 'text', ['required' => false]);
        $formMapper->add('address', 'textarea', ['required' => false]);
        $formMapper->add('postcode', 'text', ['required' => false]);
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