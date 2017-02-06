<?php

namespace Acme\ServerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userId')
            ->add('name')
            ->add('filename')
            ->add('resizeHeight')
            ->add('resizeWidth')
            ->add('isShowHost');
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Acme\ServerBundle\Entity\Picture',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }
}
