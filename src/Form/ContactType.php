<?php

namespace App\Form;

use App\Entity\Contact;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'attr' => ['class' => 'form-group form-control col-md-12', 'placeholder' => 'Tapez votre nom'],
                'required' => false
            ])
            ->add('prenom', TextType::class, [
                'attr' => ['class' => 'form-group form-control col-md-12', 'placeholder' => 'Tapez votre prenom'],
                'required' => false
            ])
            ->add('telephone', TextType::class, [
                'attr' => ['class' => 'form-group form-control col-md-12', 'placeholder' => 'Tapez votre telephone'],
                'required' => false
            ])
            ->add('email', TextType::class, [
                'attr' => ['class' => 'form-group form-control col-md-12', 'placeholder' => 'Tapez votre email'],
                'required' => false
            ])
            ->add('message', TextType::class, [
                'attr' => ['class' => 'form-group form-control col-md-12', 'placeholder' => 'Tapez votre message'],
                'required' => false
            ])
            ->add('brochure', FileType::class, [
                'attr' => ['class' => 'form-group form-control col-md-12'],
                'label' =>  'Brochure (PDF file)',
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,
                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])

                ]
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'constraints' => new Recaptcha3(),
                'action_name' => 'homepage',
                'locale' => 'fr',
            ]);;

        // Evenement : affiche le bloc nom si l'id est null
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();

            /** @var Contact */
            /*   
                $contact = $event->getData();
         
                if($contact->getId() === null) {
                    $form->add('nom', TextType::class, [
                        'attr' => ['class' => 'form-group form-control col-md-12', 'placeholder' => 'Tapez votre nom'],
                        'required' => false,
                    ]);
                } */
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
