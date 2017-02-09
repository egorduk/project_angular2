<?php

namespace Acme\ServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 50]),
                ],
            ])
            ->add('filename', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 50]),
                ],
            ])
            ->add('resizeHeight', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex('/\d+/'),
                ],
            ])
            ->add('resizeWidth', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Regex('/\d+/'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Acme\ServerBundle\Entity\Picture',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
