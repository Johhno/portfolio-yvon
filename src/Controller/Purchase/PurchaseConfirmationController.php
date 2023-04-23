<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Form\CartConfirmationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController
{
    protected $formFactory;
    protected $router;
    protected $security;
    protected $cartService;

    public function __construct(FormFactoryInterface $formFactory, RouterInterface $router, Security $security, CartService $cartService)
    {
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->security = $security;
        $this->cartService = $cartService;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request, FlashBagInterface $flashBag)
    {
        // 1. Nous voulons lire les données du formulaire => FormFactoryInterface/Request
        $form = $this->formFactory->create(CartConfirmationType::class);
        $form->handleRequest($request);

        // 2. Si le formulaire n'a pas été soumis : dégager
        if (!$form->isSubmitted()) {
            // Message Flash puis redirection()
            $flashBag->add('warning', 'Vous devez remplir le formulaire de confirmation.');
            return new RedirectResponse($this->router->generate('cart_show'));
        }

        // 3. Si je ne suis pas connecté : dégager(Security)
        $user = $this->security->getUser();
        if (!$user) {
            throw new AccessDeniedException("Vous devez être connecté pour confirmer une commande.");
        }

        // 4. Si il n'ya pas de produits dans mon panier : dégager (CartService)
        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $flashBag->add('warning', 'Vous ne pouvez confirmer une commande avec un panier vide.');
            return new RedirectResponse($this->router->generate('cart_show'));
        }

        $data = $form->getData();
        //dd($form->getData());

        // 5. Nous allons créer une purchase
        $purchase = new Purchase();
        $purchase->setFullName($data['fullName'])
            ->setAddress($data['address'])
            ->setPostalCode($data['postalCode'])
            ->setCity($data['city']);
        //dd($purchase);
        // 6. Nous allons la lier avec l'utilisateur actuellement connecté (Security)

        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService)

        // 8. Nous allons enregister la commande (EntityManagerInterface)
    }
}
