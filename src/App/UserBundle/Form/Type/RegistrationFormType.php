<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $countryList = array('france');
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


    public function getName()
    {
        return 'app_user_registration';
    }
}
