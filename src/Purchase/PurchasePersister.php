<?php

namespace App\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(Security $security, CartService $cartService, EntityManagerInterface $em)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function storePurchase(Purchase $purchase)
    {
        // Intégrer tout ce qu'il faut et persister la purchase

        // 6. Nous allons la lier avec l'utilisateur actuellement connecté (Security)
        $purchase->setUser($this->security->getUser())
            ->setPurchasedAt(new DateTime());

        // 7. Nous allons la lier avec les produits qui sont dans le panier (CartService)
        foreach ($this->cartService->getDetailedCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal());

            $this->em->persist($purchaseItem);
        }

        // 8. Nous allons enregister la commande (EntityManagerInterface)
        $purchase->setTotal($this->cartService->getTotal());
        $this->em->persist($purchase);

        $this->em->flush();
    }
}
