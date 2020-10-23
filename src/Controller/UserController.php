<?php


namespace App\Controller;


use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * Formullaire d'inscription d'un User
     * @Route("/membre/inscription", name="user_create", methods={"GET|POST"})
     */
   public function createUser(Request $request, UserPasswordEncoderInterface $encoder)
   {
      #1. Creation d'un objet user
       $user = new User();
       $user->setRoles(['ROLE_USER']);

       #2. Creation du Formulaire
       $form = $this->createFormBuilder($user)
           ->add('firstname', TextType::class)
           ->add('lastname', TextType::class)
           ->add('email', EmailType::class)
           ->add('password', PasswordType::class)
           ->add('submit', SubmitType::class)
           ->getForm();

           $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
           #3. Encodege du MDP
           $user->setPassword(
               $encoder->encodePassword($user, $user->getPassword())
           );
           #4. Sauvegarde de BDD
           $em = $this->getDoctrine()->getManager();#Recupération du EM
           $em->persist($user);#Demande pour sauvegarder en BDD $user
           $em->flush();#On execute la demande

           #5. Notification Flash
           $this->addFlash('notice', 'Fellisitation pour votre inscription !');
           #6. Redirection FIXME modifier l'url vers page connexion
           return $this->redirectToRoute('index');
       } #endif

       return  $this->render( 'user/create.html.twig', [
           'form' =>$form->createView()
       ]);
   }
}