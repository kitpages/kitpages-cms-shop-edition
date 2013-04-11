<?php

namespace App\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'category',
            'choice',
            array(
//                'label' => 'Category',
                'required' => false
            ,
                'choices'   => array(
                    'cat1' => array('cat1-ss1' => 'cat1-ss1', 'cat1-ss2' => 'cat1-ss2'),
                    'top' => 'Top',
                    'center' => 'Centered',
                    'bottom' => 'Bottom',
                    'hidden' => 'Hidden'
                )
            )
        );
        $builder->add(
            'senderEmail',
            "email",
            array(
                "label" => "Votre email :",
                "attr" => array(
                    "size"=>40
                )
            )
        );
        $builder->add(
            'senderName',
            "text",
            array(
                "label" => "Votre nom :",
                "attr" => array(
                    "size"=>30
                )
            )
        );
        $builder->add(
            'subject',
            "text",
            array(
                "label" => "Sujet :",
                "attr" => array(
                    "size"=>40
                )
            )
        );
        $builder->add(
            'message',
            "textarea",
            array(
                "label" => "Message :",
                "attr" => array(
                )
            )
        );
    }

    public function getName()
    {
        return 'app_sitebundle_contacttype';
    }
}
