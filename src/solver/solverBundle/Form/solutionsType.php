<?php

namespace solver\solverBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class solutionsType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('libelle')            
            ->add('probleme','entity',array('class'=>'solversolverBundle:problemes',
                                          'property'=>'libelle',
                                          'required'=>true,                                          
                                          )
                 )
            ->add('existe','checkbox',array('required'=>false))
        ;
    }

    public function getName()
    {
        return 'solver_solverbundle_solutionstype';
    }
    
    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'solver\solverBundle\Entity\solutions',);
    }
}
