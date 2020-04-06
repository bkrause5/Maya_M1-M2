<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;


class ProduitController extends AbstractController
{
    /**
     * @Route("/produit/complet/creer", name="produit_complet_creer")
     */
    public function creerProduitComplet()
    {
        // créer une catégorie
        $categorie = new Categorie();
        $categorie->setLibelle('Fruits');

        // créer un produit
        $produit = new Produit();
        $produit->setLibelle('mirabelle');
        $produit->setPrix(2.50);

        // mettre en relation le produit avec la catégorie
        $produit->setCategorie($categorie);

        // persister les objets
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($categorie);
        $entityManager->persist($produit);
        // exécutez les requêtes
        $entityManager->flush();

        // retourner une réponse
        return new Response(
            'Nouveau produit enregistré avec l\'id : '.$produit->getId()
            .' et nouvelle catégorie enregistrée avec id: '.$categorie->getId()
        );
    }

    /**
     * @Route("/produit", name="produit")
     */
    public function index()
    {
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }

    /**
     * @Route("/produit/creer", name="produit_creer")
     */
    public function creerProduit(EntityManagerInterface $entityManager): Response
    {
        // : Response        type de retour de la méthode creerProduit
        // pour récupérer le EntityManager
        //     on peut ajouter l'argument à la méthode comme ici  creerProduit(EntityManagerInterface $entityManager)
        //     ou on peut récupérer le EntityManager via $this->getDoctrine() comme ci-dessus en commentaire
        //        $entityManager = $this->getDoctrine()->getManager();

        // créer l'objet
        $produit = new Produit();
        $produit->setLibelle('haricots verts');
        $produit->setPrix(2.60);

        // dire à Doctrine que l'objet sera (éventuellement) persisté
        $entityManager->persist($produit);

        // exécuter les requêtes (indiquées avec persist) ici il s'agit de l'ordre INSERT qui sera exécuté
        $entityManager->flush();

        return new Response('Nouveau produit enregistré, son id est : '.$produit->getId());
    }



    /**
     * @Route("/produit/{id}", name="produit_lire")
     */
    public function lire($id)
    {
        // {id} dans la route permet de récupérer en argument $id
        // on utilise le Repository de la classe Produit
        // il s'agit d'une classe qui est utilisée pour les recherches d'entités (et donc de données dans la base)
        // la classe ProductRepository a été créée en même temps que l'entité par le make
        $produit = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->find($id);

        if (!$produit) {
            throw $this->createNotFoundException(
                'Ce produit n\'existe pas : '.$id
            );
        }

        return new Response('Voici le libellé du produit : '.$produit->getLibelle());
        // on peut bien sûr également rendre un template


    }

    /**
     * @Route("/produitautomatique/{id}", name="produitautomatique_lire")
     */
    public function lireautomatique(Produit $produit)
    {
        // grâce au SensioFrameworkExtraBundle (installé ici car création application complète)
        // il suffit de donner le produit en argument
        // la requête de recherche sera automatique
        //et une page 404 sera générée si le produit n'existe pas

        return new Response('Voici le libellé du produit lu automatiquement : '
            .$produit->getLibelle().
            ' crée le '.$produit->getDateCreation()->format('Y-m-d H:i:s'));
        // on peut bien sûr également rendre un template
    }


    /**
     * @Route("/produit/modifier/{id}", name="produit_modifier")
     */
    public function modifier($id)
    {
        // 1  recherche du produit
        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        // en cas de produit inexistant, affichage page 404
        if (!$produit) {
            throw $this->createNotFoundException(
                'Aucun produit avec l\'id '.$id
            );
        }

        // 2 modification des propriétés
        $produit->setLibelle('haricots verts fins');
        // 3 exécution de l'update
        $entityManager->flush();

        // redirection vers l'affichage du produit
        return $this->redirectToRoute('produit_lire', [
            'id' => $produit->getId()
        ]);
    }


    /**
     * @Route("/produit/supprimer/{id}", name="produit_supprimer")
     */
    public function supprimer($id)
    {
        // 1  recherche du produit
        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(Produit::class)->find($id);

        // en cas de produit inexistant, affichage page 404
        if (!$produit) {
            throw $this->createNotFoundException(
                'Aucun produit avec l\'id '.$id
            );
        }

        // 2 suppression du produit
        $entityManager->remove(($produit));
        // 3 exécution du delete
        $entityManager->flush();

        // affichage réponse
        return new Response('Le produit a été supprimé, id : '.$id);
    }


    /**
     * @Route("/produits/{prix}", name="produits_lireProduits")
     */
    public function lireProduits($prix)
    {
        $produits = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findAllGreaterThanPrice($prix);

        // OU
        // $repository = $this->getDoctrine()->getRepository(Produit::class);
        // $produits = $repository->findAllGreaterThanPrice($prix);

        // OU
        //  ajouter :                   use App\Repository\ProduitRepository;
        // injecter le repository :      public function lireProduits($prix, ProduitRepository $repository)
        //  et écrire    :      $produits = $repository->findAllGreaterThanPrice($prix);

        return new Response('Voici le nombre de produits : '.sizeof($produits));
    }











}
