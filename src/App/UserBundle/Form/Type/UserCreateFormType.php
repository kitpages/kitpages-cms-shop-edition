<?php

namespace App\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use App\UserBundle\Entity\Group;

class UserCreateFormType extends BaseType
{

    protected $securityContext;
    protected $translator;

    /**
     * @param string $class The User class name
     */
    public function __construct($class, $translator)
    {
        parent::__construct($class);

        $this->translator = $translator;

    }

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

        $builder->add('group', 'entity', array(
            'class' => 'App\\UserBundle\\Entity\\Group',
            'empty_value' => $this->translator->trans('Choose an group'),
            'required' => true
        ));

    }


    public function getName()
    {
        return 'app_user_registration';
    }
}
