<?php

namespace App\Form\TypeExtension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpFormExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($options['help']) {
            $view->vars['help'] = $options['help'];
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('help', null);
    }

    public static function getExtendedTypes()
    {
        //modify every form type in the system
        return array(FormType::class);
    }
}
