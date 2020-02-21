<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class ExportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'entity',
                ChoiceType::class,
                [
                    'label' => 'Wybierz dane',
                    'choices' => array_flip($this->entities())
                ]
            )
            ->add(
                'format',
                ChoiceType::class,
                [
                    'label' => 'Wybierz format',
                    'choices' => array_flip($this->formats())
                ]
            )
            ->add(
                'backToImport',
                ChoiceType::class,
                [
                    'label' => 'Plik zdolny do importu?',
                    'choices' => $this->backToImport()
                ]
            )
            ->add(
                'dataId',
                TextType::class,
                [
                    'label' => 'Id encji',
                    'help' => 'Pozostaw puste, jeśli chcesz wybrać wszystkie dane'
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Wyślij',
                ]
            );
    }

    private function entities(): array
    {
        return [
            'user' => 'User',
            'genus' => 'Genus',
            'genusNote' => 'GenusNote',
            'genusScientist' => 'genusScientist',
            'subFamily' => 'SubFamily',
        ];
    }

    private function formats(): array
    {
        return [
            'json' => 'Json',
            'xml' => 'Xml',
            'yaml' => 'Yaml',
            'csv' => 'csv',
        ];
    }

    private function backToImport(): array
    {
        return [
            'Tak' => true,
            'Nie' => false,
        ];
    }
}
