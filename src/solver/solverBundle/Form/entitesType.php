<?php

namespace solver\solverBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class entitesType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('libelle','text')
            ->add('existe','checkbox',array('required'=>false))            
        ;
    }

    public function getName()
    {
        return 'solver_solverbundle_entitestype';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'solver\solverBundle\Entity\entites',);
    }
}
