<?php

namespace Acme\DemoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FridgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('fridge_csv', 'file',
        array('label' => 'Import ingrediants')
        );
        $builder->add('recipe', 'textarea',
        array(
        'attr' => array('cols' => '40', 'rows' => '10'),
        
        ));
    }

    public function getName()
    {
        return 'fridge';
    }
}
