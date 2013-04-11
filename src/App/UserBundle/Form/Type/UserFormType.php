<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;


class UserFormType extends AbstractType
{

    public function getDefaultOptions(array $options)
    {
        return array();
    }

    public function getName()
    {
        return 'fos_user_profile_form_user';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $countryList = array('france');
        $builder->add('username')
            ->add('email', 'email')
            ->add('firstname', 'text')
            ->add('lastname', 'text');

        $builder->add(
            'country',
            'country',
            array(
                'required' => true
            )
        );

    }

}
