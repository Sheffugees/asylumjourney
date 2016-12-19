<?php

namespace Application\Sonata\UserBundle\Admin\Model;

use FOS\UserBundle\Model\UserManagerInterface;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\CallbackTransformer;

class UserAdmin extends Admin
{

    /**
     * {@inheritdoc}
     */
    public function getFormBuilder()
    {
        $this->formOptions['data_class'] = $this->getClass();

        $options = $this->formOptions;
        $options['validation_groups'] = (!$this->getSubject() || is_null(
                $this->getSubject()->getId()
            )) ? 'Registration' : 'Profile';

        $formBuilder = $this->getFormContractor()->getFormBuilder($this->getUniqid(), $options);

        $this->defineFormBuilder($formBuilder);

        return $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        // avoid security field to be exported
        return array_filter(
            parent::getExportFields(),
            function ($v) {
                return !in_array($v, array('password', 'salt'));
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('email')
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true))
            ->add('createdAt');

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add(
                    'impersonating',
                    'string',
                    array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig')
                );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $filterMapper)
    {
        $filterMapper
            ->add('id')
            ->add('username')
            ->add('locked')
            ->add('email');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('General')
            ->add('username')
            ->add('email')
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('username')
            ->add('email')
            ->add(
                'plainPassword',
                'text',
                array(
                    'required' => (!$this->getSubject() || is_null($this->getSubject()->getId()))
                )
            )
            ->end();

        if ($this->getSubject() && !$this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->with('Management')
                ->add(
                    'roles',
                    'choice',
                    [
                        'label' => 'User type',
                        'choices' => [
                            'Editor' => 'ROLE_EDITOR',
                            'Admin' => 'ROLE_SUPER_ADMIN',
                        ],
                        'expanded' => true,
                        'multiple' => false,
                        'choices_as_values' => true
                    ]
                )
                ->end();

            $formMapper->get('roles')
                ->addModelTransformer(new CallbackTransformer(
                    function ($rolesAsArray) {
                        return isset($rolesAsArray[0]) ? $rolesAsArray[0]: '';
                    },
                    function ($role) {
                        return [$role];
                    }
                ))
            ;
        }

    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }
}
