<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{

    /**
     * @Route("/contact", name="envoyerMessageContact")
     */
    public function envoyerMessageContact(Request $request, EntityManagerInterface $em, ValidatorInterface $vi)
    {

        //getForm + setData
        $form = $this->createForm(ContactType::class);
        //analyse request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $contact = $form->getData();

            $em->persist($contact);
            $em->flush();
            dd($contact);
        }

        $formView = $form->createView();

        return $this->render('contact/contact.html.twig', [
            'formView' => $formView
        ]);
    }


    /**
     * @Route("/admin/{id}/editerMessageContact", name="messageContactEdit")
     */
    public function editerMessageContact(
        $id,
        ContactRepository $contactRepository,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ) {

        $contact = new Contact();

        $contact->setNom('Tchen');
        $erreurs  = $validator->validate($contact);

        //Compte le nombre d'erreurs
        if ($erreurs->count() > 0) {
            dd("Il y a des erreurs", $erreurs);
        }
        dd("Tout va bien");
        $message = $contactRepository->find($id);

        $form = $this->createForm(ContactType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        $formView = $form->createView();

        return $this->render('contact/edit.html.twig', [
            'contact' => $message,
            'formView' => $formView
        ]);
    }
}
