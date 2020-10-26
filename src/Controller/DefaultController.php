<?php


namespace App\Controller;



use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * Page/ Action : Accueil
     */
    public function index()
    {
        # Recuperer les  6 dérniers articles de la BDD order décroissant
        #->getRepository(XXX::class) : L'entitée ou je souhaite récupérer les données
        #->findBy() : Récupère les données selon plusiers critères
        #->findOneBy() : Récupère un enregistrement selon plusiers critères
        #->findAll() : Récupère toutes les données de la table
        #->find(id) : Récupère une donée via son ID
        $posts = $this->getDoctrine()
            ->getRepository( Post::class)
            ->findBy([], ['id' => 'DESC'], 6);

        # Transmettre à la vue
      return $this->render('default/index.html.twig', ['posts' => $posts]);

    }
    /**
     * Page/Contact
     */
   public function contact()
   {
       return $this->render('default/contact.html.twig');
   }

    /**
     * Page/Action : Categorie
     * Permet d'afficher les articles d'une catégorie
     * @Route("/{alias}", name="default_category", methods={"GET"})
     */
    public function category($alias)
    {
        # Recuperation de la catégorie via son alias dans l'URL
        $category = $this->getDoctrine()
            ->getRepository( Category::class)
            ->findOneBy(['alias' => $alias]);
        /**
         * Grâce à la relation entre Post et Category
         * (OneToMany), je suis en mesure de récupérer
         * les articles de la catégorie.
         */
        $posts = $category->getPosts();

        return $this->render('default/category.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * Page/Action : Article
     * Permet d'afficher un article du site
     * @Route("/{category}/{alias}_{id}.html", name="default_article", methods={"GET"})
     */
    public  function  post($id)
    {
        # Récupérer l'article via son ID
        $post = $this->getDoctrine()
            ->getRepository( Post::class)
            ->find($id);
        https://localhost:8000/politique/couvre-feu-quand-la-situation-sanitaire-s-ameliorera-t-elle_14155614.html
        return $this->render('default/article.html.twig', [
           'post' => $post
        ]);

    }
}