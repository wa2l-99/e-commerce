<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'label' => 'Ajouter mon adresse',
                'attr'=>[
                    'placeholder' =>'Nommez votre addresse'
                ]
            ])
            ->add('firstname',TextType::class,[
                'label' => 'Votre prenom',
                'attr'=>[
                    'placeholder' =>'Entre votre prenom'
                ]
            ])
            ->add('lastname',TextType::class,[
                'label' => 'Votre Nom',
                'attr'=>[
                    'placeholder' =>'Entre votre nom'
                ]
            ])
            ->add('company',TextType::class,[
                'label' => 'Votre sociéte',
                'required' => false,
                'attr'=>[
                    'placeholder' =>'(facultatif) Entre le nom de votre societe'
                ]
            ])
            ->add('address',TextType::class,[
                'label' => 'Votre adresse',
                'attr'=>[
                    'placeholder' =>'8 rue des lylas '
                ]
            ])
            ->add('postale',TextType::class,[
                'label' => 'Votre code poostale',
                'attr'=>[
                    'placeholder' =>'Entre votre code postal'
                ]
            ])
            ->add('city',TextType::class,[
                'label' => 'Ville ',
                'attr'=>[
                    'placeholder' =>'Entre votre ville'
                ]
            ])
            ->add('phone',TelType::class,[
                'label' => 'Votre telephone ',
                'attr'=>[
                    'placeholder' =>'Entre votre telephone'
                ]
            ])
            ->add('submit',SubmitType::class,[
                'label' => 'Valider',
                'attr'=>[
                    'class'=>'btn btn-block btn-info'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
