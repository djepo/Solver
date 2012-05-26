<?php

namespace solver\solverBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class entitesType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('libelle')
        ;
    }

    public function getName()
    {
        return 'solver_solverbundle_entitestype';
    }
}
