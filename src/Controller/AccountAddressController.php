<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    private $enttityManager ;

    public function __construct(EntityManagerInterface $enttityManager)
    {
        $this->enttityManager = $enttityManager;
    }

    /**
     * @Route("/compte/addresses", name="account_address")
     */
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }


    /**
     * @Route("/compte/ajouter-une-adresse", name="account_address_add")
     */
    public function add(Request $request): Response
    {

        $address = new Address();
        $form = $this->createForm(AddressType::class,$address);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){
            $address->setUser($this->getUser());
            $this->enttityManager->persist($address);
            $this->enttityManager->flush();
            return $this->redirectToRoute('account_address');

        }
        return $this->render('account/address-form.html.twig',[
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/compte/modifier-une-adresse/{id}", name="account_address_modifier")
     */
    public function modifier(Request $request,$id): Response
    {

        $address = $this->enttityManager->getRepository(Address::class)->findOneById($id);

        if(!$address || $address->getUser()!= $this->getUser() ){
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class,$address);
        $form->handleRequest($request);
        if ($form->isSubmitted()&& $form->isValid()){

            $this->enttityManager->flush();
            return $this->redirectToRoute('account_address');

        }
        return $this->render('account/address-form.html.twig',[
            'form'=>$form->createView(),
        ]);
    }
    /**
     * @Route("/compte/delete-une-adresse/{id}", name="account_address_delete")
     */
    public function delete($id): Response
    {

        $address = $this->enttityManager->getRepository(Address::class)->findOneById($id);

        if($address && $address->getUser()== $this->getUser() ){
            $this->enttityManager->remove($address);
            $this->enttityManager->flush();
            return $this->redirectToRoute('account_address');
        }
    }
}
