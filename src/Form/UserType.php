<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    private $stack;
    public function __construct(RequestStack $requestStack)
    {
        $this->stack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $route = $this->stack->getCurrentRequest()->get('_route');
        $builder
            ->add('email', TextType::class, [
                'label' => 'Mail',
                'attr'  => [
                    'class' => 'form-control'
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prenom',
                'attr'  => [
                    'class' => 'form-control'
                ],
            ])

            ->add('civility', ChoiceType::class, [
                'label'    => 'Civilité',
                'attr'     => [
                    'class' => '',
                ],
                'row_attr' => [
                    'class' => 'form-check form-check-custom form-check-solid'
                ],
                'choice_attr'=> function($choice, $key, $value) {
                    return ['class' => 'form-check-input h-30px'.strtolower($key)];
                },

                'expanded' => true,
                'choices'  => array_flip(User::CIVILITY),
                'required' => true

            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr'  => [
                    'class' => 'form-control'
                ],
            ]);
        if ($route === 'app_user_new') {

        $builder
            ->add('password' ,PasswordType::class, [
                'attr'=>[
                    'class' => 'form-control input100'
                ],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);}

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
