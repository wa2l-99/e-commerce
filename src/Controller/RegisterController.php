<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    //injection des dependances

    private $entityManager;
    
    //Lors de la creation de la classe RegisterController il faut instentie EntityManagerInterface 
    //entityManager: permet de chercher les informations de la BD grace a l'ORM Doctrine

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }


    /**
     * @Route("/inscription", name="register")
     */


    //Lors de la function index il faut construire la request

    public function index(Request $request,UserPasswordEncoderInterface $encoder ): Response
    {

        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $password = $encoder->encodePassword($user,$user->getPassword());

            //reinjecte le password dans l'objet user
            $user->setPassword($password);

            $this->entityManager->persist($user);

            $this->entityManager->flush();

        }

        return $this->render('register/index.html.twig',[

            'form' => $form->createView()
        
        ]);
    }

}
