<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ResourceLinkAdmin extends Admin
{
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('name', 'text');
        $formMapper->add('url', 'text');
        $formMapper->add('expiryDate', 'date', ['required' => false]);
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
        $showMapper->add('name');
        $showMapper->add('url');
        $showMapper->add('expiryDate');
    }
}