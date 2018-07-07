<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;

class ShortDateAdmin extends AbstractAdmin
{
    public function getDataSourceIterator()
    {
        $datasourceit = parent::getDataSourceIterator();
        $datasourceit->setDateTimeFormat('d-M-Y');

        return $datasourceit;
    }
}