<?php
//contient tous la mécanique du panier (Ajout, Suppression, Affichage, ) et gére les operations liee au panier .

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }


    public function get()
    {
        return $this->session->get('cart');
    }

    //Supprimer completement le panier 

    public function remove()
    {
        return $this->session->remove('cart');
    }

    // Supprimer juste un produit
    public function delete($id)
    {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);
        
        return $this->session->set('cart', $cart);

    }

    // Supprimer une quantité
    public function decrease($id)
    {

        $cart = $this->session->get('cart', []);

        // verifier si la quantité du produit = 1

        if ($cart[$id] > 1){

            //retirer la quantité (faire -1)
            $cart[$id]--;
        }else{

            unset($cart[$id]);

            // supprimer le produit 
        }
        
        return $this->session->set('cart', $cart);
    }

    public function getFull(){

         //Mecanisme pour chercher et recuperer tout nos produits associés a ce que on a dans le panier

         $cartComplete= [];

         if ($this->get()){
 
             foreach ($this->get() as $id => $quantity) {

                $product_object= $this->entityManager->getRepository(Product::class)->findOneById($id);
                
                if(!$product_object){

                    $this->delete($id);
                    continue;

                }

                $cartComplete[]= [
 
                     'product' => $product_object,
                     'quantity' => $quantity
 
 
                 ];
             }
         }
         
         return $cartComplete;
    }
}

