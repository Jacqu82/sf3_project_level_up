<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class ImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'file',
                FileType::class,
                [
                    'label' => 'Wybierz plik do exportu',
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Wyślij'
                ]
            );
    }
}
