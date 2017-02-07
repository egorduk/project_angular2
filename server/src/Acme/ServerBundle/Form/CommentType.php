<?php

namespace Acme\ServerBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(array('max' => 50)),
                ],
            ])
            ->add('picture', EntityType::class, [
                'class' => 'Acme\ServerBundle\Entity\Picture',
                'choice_label' => 'id',
            ])
            /*->add('user', EntityType::class, [
                'class' => 'Acme\ServerBundle\Entity\User',
                'choice_label' => 'id',
            ])*/;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Acme\ServerBundle\Entity\PictureComment',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }

    public function getName()
    {
        //return 'acme_server_bundle_comment';
    }
}
