<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{

    /**
     * Formullaire permettant de crée un article
     * @Route("/article/creer-un-article", name="post_create", methods={"GET|POST"})
     */
    public function createPost(Request $request, SluggerInterface $slugger)
    {
        #1.Création d'un nouveau Post
        $post = new Post();

        #1b. Attribution d'un user
        # FIXME Temporaire
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->find(1);

        $post->setUser($user);

        #1c. Ajout de la date de rédaction
        $post->setCreatedAt(new \DateTime());

        #2.Création d'un foormulaire avec $post
        $form = $this->createFormBuilder($post)

            #2a.Titre de l'article
            ->add('title', TextType::class)
            #->add()
            #2b. Categorie de l'article (Liste déroulante)
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,
                'choice_label' => 'name',

            ])
            #2c. Contenu de l'article
            ->add('content', TextareaType::class)
            #2d. Upload Image de l'article
            ->add('featuredImage', FileType::class)
            #2e. Bouton Submit de l'article
            ->add('submit', SubmitType::class)
            #Permet de récupérer le formulaire généré
            ->getForm();
        #Demande à Symfony de récupérer les infos dans la request.
        $form->handleRequest($request);

        # Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            #4a. TODO Gestion Upload de l'image
            #4b. Génération de l'alias
            $post->setAlias(
                $slugger->slug(
                    $post->getTitle()
                )
            );
            #4c. Sauvegarde dans la BDD
            /**
             * Qu'est ce que le Entity Manager(em)?
             * C'est une classe qui sait comment sauvegarder d'autres classes.
             */
            $em = $this->getDoctrine()->getManager();#Recupération du EM
            $em->persist($post);#Demande pour sauvegarder en BDD $post
            $em->flush();#On execute la demande
            #4d. Notification / Confirmation
            $this->addFlash('notice', 'Votre article est en ligne !');
            #4e. Redirection
            return $this->redirectToRoute('default_article', [
                'category' => $post->getCategory()->getAlias(),
                'alias' =>$post->getAlias(),
                'id' => $post->getId()
            ]);

        }

        #Transmission du formulaire àla vue
        return $this->render('post/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}