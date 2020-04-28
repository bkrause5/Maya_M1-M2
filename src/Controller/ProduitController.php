<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
//use App\Entity\Categorie;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;



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
    public function index(Request $request, ProduitRepository $repository, SessionInterface $session, PaginatorInterface $paginator)
    {
        // créer l'objet et le formulaire de recherche
        $produitRecherche = new ProduitRecherche();
        $formRecherche = $this->createForm(ProduitRechercheType::class, $produitRecherche);
        $formRecherche->handleRequest($request);
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            $produitRecherche = $formRecherche->getData();
            // cherche les produits correspondant aux critères, triés par libellé
            // requête construite dynamiquement alors il est plus simple d'utiliser le querybuilder
            $lesProduits =$repository->findAllByCriteria($produitRecherche);
            // mémoriser les critères de sélection dans une variable de session
            $session->set('ProduitCriteres', $produitRecherche);
            $lesProduits= $paginator->paginate(
                $repository->findAllByCriteria($produitRecherche),
                $request->query->getint('page',1),
                5
            );
        } else {
            // lire les produits
            if ($session->has("ProduitCriteres")) {
                $produitRecherche = $session->get("ProduitCriteres");
//                $lesProduits = $repository->findAllByCriteria($produitRecherche);
                $lesProduits= $paginator->paginate(
                    $repository->findAllByCriteria($produitRecherche),
                    $request->query->getint('page',1),
                    5
                );
                $formRecherche = $this->createForm(ProduitRechercheType::class, $produitRecherche);
                $formRecherche->setData($produitRecherche);
            } else {
                $p=new ProduitRecherche();
                $lesProduits= $paginator->paginate(
                    $repository->findAllByCriteria($p),
                    $request->query->getint('page',1),
                    5
                );
//                $lesProduits = $repository->findAllOrderByLibelle();
            }
        }

        return $this->render('produit/index.html.twig', [
            'formRecherche' => $formRecherche->createView(),
            'lesProduits' => $lesProduits,
        ]);

    }







    /**
     * @Route("/produit/ajouter", name="produit_ajouter")
     */
    public function ajouter(Produit $produit=null, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire d'ajout a été soumis par l'utilisateur et est valide
            $produit = $form->getData();
            // on met à jour la base de données
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le produit ' . $produit->getLibelle() . ' a été ajouté.'
            );
            return $this->redirectToRoute('produit');
        } else {
            // cas où l'utilisateur a demandé l'ajout, onaffiche le formulaire d'ajout
            return $this->render('produit/ajouter.html.twig', [
                'form' => $form->createView(),
            ]);
        }
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

    /**
     * @Route("/produit/modifier/{id<\d+>}", name="produit_modifier")
     */
    public function modifier(Produit $produit = null, Request $request, EntityManagerInterface $entityManager)
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // cas où le formulaire  a été soumis par l'utilisateur et est valide
            //pas besoin de "persister" l'entité : en effet, l'objet a déjà été retrouvé à partir de Doctrine ORM.
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le produit '.$produit->getLibelle().' a été modifié.'
            );

            return $this->redirectToRoute('produit');
        }
        // cas où l'utilisateur a demandé la modification, on affiche le formulaire pour la modification
        return $this->render('produit/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/produit/supprimer/{id<\d+>}", name="produit_supprimer")
     */
    public function supprimer(Produit $produit, Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->isCsrfTokenValid('action-item'.$produit->getId(), $request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Le produit '.$produit->getLibelle().' a été supprimé.'
            );

            return $this->redirectToRoute('produit');
        }
    }















}
