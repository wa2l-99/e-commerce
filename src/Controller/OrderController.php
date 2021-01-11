<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;



class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    
    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart,Request $request): Response
    {
        // si l'utilisatuer n'a pas encore une addresse rederiger vers le fomulaire d'ajout d'addresse
        if (!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }


        $form = $this->createForm(OrderType::class, null, [
            //affiche l'adresse uniquement de l'utlisateur connecté en cours 
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull(),
        ]);


    }
    //POST: accepte que les utlisateurs,les requettes qui viennent d'un poste 
     /**
     * @Route("/commande/recapitulatif", name="order-recap", methods={"POST"})
     */
    public function add(Cart $cart,Request $request): Response
    {


        $form = $this->createForm(OrderType::class, null, [
            //affiche l'adresse uniquement de l'utlisateur connecté en cours 
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
          $date = new \DateTime();
          $delivery = $form->get('addresses')->getData();
          $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
          $delivery_content .= '<br/>'.$delivery->getPhone();

          if ($delivery->getCompany()) {
            $delivery_content .= '<br/>'.$delivery->getCompany();
          }

          $delivery_content .= '<br/>'.$delivery->getAddress();
          $delivery_content .= '<br/>'.$delivery->getPostale().' '.$delivery->getCity();

        
          //enregistrer ma commande -> entité order()
          $order = new Order();
          $reference = $date->format('dmY').'-'.uniqid();
          $order->setReference($reference);
          $order->setUser($this->getUser());
          $order->setCreatedAt($date);
          $order->setDelivery($delivery_content);
          $order->setIsPaid(0);

          $this->entityManager->persist($order);


          //enregistrer mes produits -> entité orderDetails()
          foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

            $this->entityManager->flush();
            // affiche cette route pour la confirmatoin de commande si le form est soumi
            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'delivery' => $delivery_content,
                'reference' => $order->getReference()


            ]);
        
        }

        // si non retourne à la page des produits 
        return $this->redirectToRoute('cart');


    }
}
