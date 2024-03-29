<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
			->add('lastName', TextType::class,[
				'label' => 'Nom',
				'attr' => [
					'placeholder' => 'Nom'
				]
			])
			->add('firstName', TextType::class,[
				'label' => 'Prénom',
				'attr' => [
					'placeholder' => 'Prénom'
				]
			])
			->add('address', TextType::class,[
				'label' => 'Adresse',
				'attr' => [
					'placeholder' => 'Adresse'
				]
			])
			->add('addressCode', TextType::class,[
				'label' => 'Code Postal',
				'attr' => [
					'placeholder' => 'Code Postal'
				]
			])
			->add('city', TextType::class,[
				'label' => 'Ville',
				'attr' => [
					'placeholder' => 'Ville'
				]
			])
            ->add('email', EmailType::class, [
				'label' => 'Votre Email',
				'attr' => [
						'placeholder' => 'Email'
				]
				// 'required' => false
			])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
