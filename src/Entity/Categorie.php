<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategorieRepository")
 */
class Categorie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Produit", mappedBy="categorie")
     */
    private $produits;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setCategorie($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->contains($produit)) {
            $this->produits->removeElement($produit);
            // set the owning side to null (unless already changed)
            if ($produit->getCategorie() === $this) {
                $produit->setCategorie(null);
            }
        }

        return $this;
    }

    /**
     * @Route("/produit/complet/{creer}", name="produit_complet_creer")
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

}
