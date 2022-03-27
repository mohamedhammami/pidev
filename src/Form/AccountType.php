<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Balance', TextType::class, [
                'label' => 'Montant',
                'attr'  => [
                    'class' => 'form-control',
                    'required' => true
                ]
            ])
            ->add('rib', TextType::class, [
                'label' => 'RIB',
                'attr'  => [
                    'class' => 'form-control',
                ]
            ])
            ->add('type', ChoiceType::class, [
                'attr'     => [
                    'class' => ' form-control',
                ],
                'choices'=>[
                    'Compte courant' => 1,
                    'Compte epargne' => 2,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
