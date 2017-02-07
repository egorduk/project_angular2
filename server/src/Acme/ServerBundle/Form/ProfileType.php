<?php

namespace Acme\ServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('regInfo', RegistrationType::class)
            ->add('info', TextType::class, [
                'constraints' => [
                    new Length(array('max' => 50)),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Acme\ServerBundle\Entity\User',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'cascade_validation' => true,
        ]);
    }
}
