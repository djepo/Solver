<?php

namespace solver\solverBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class problemesType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('libelle')
            //->add('entite')
            ->add('entite','entity',array('class'=>'solversolverBundle:entites',
                                          'property'=>'libelle'
                                          )
                 )
        ;
    }

    public function getName()
    {
        return 'solver_solverbundle_problemestype';
    }
}
