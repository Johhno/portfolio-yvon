<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var ProductRepository
     */
    protected $productRepository;


    public function __construct(SessionInterface $session, ProductRepository $productRepository)
    {
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        return $this->session->set('cart', $cart);
    }

    public function add(int $id)
    {
        // 1. Retouver le panier dans la session ( sous forme de tableau)
        // 2. S'il n'existe pas encore alors prendre un tableau vide

        $cart = $this->getCart();

        // 3. Voir si le produit ($id) existe déjà dans le tableau
        // 4. Si c'est le cas, augmenter la quantité
        // 5. Sinon ajouter le produit avec la quantité 1
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;

        // 6. Enregistrer le tableau mis à jour dans la session
        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {

        $cart = $this->getCart();

        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) {
            $this->remove($id);
        }
        $cart[$id]--;

        $this->saveCart($cart);
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function empty()
    {
        $this->saveCart([]);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }
            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
    }
}
