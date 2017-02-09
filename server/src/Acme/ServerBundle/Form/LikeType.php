<?php

namespace Acme\ServerBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class LikeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('picture', EntityType::class, [
                'class' => 'Acme\ServerBundle\Entity\Picture',
                'choice_label' => 'id',
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Acme\ServerBundle\Entity\PictureLike',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }

    public function getName()
    {
    }
}
