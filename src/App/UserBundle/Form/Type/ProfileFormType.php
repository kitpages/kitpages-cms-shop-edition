<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{

    public function getDefaultOptions(array $options)
    {
        return array();
    }

    public function getName()
    {
        return 'app_user_profile';
    }

    protected function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email')
        ;
        $builder->add('firstname', 'text');
        $builder->add('lastname', 'text');
        $builder->add(
            'country',
            'country',
            array(
                'required' => true
            )
        );
    }

}
