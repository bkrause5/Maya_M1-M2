<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */
class Produit
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank
     * @Assert\Length(
     *     min = 3,
     *     max = 40,
     *     minMessage = "Libellé trop court",
     *     maxMessage = "Libellé trop long"
     * )
     */
    private $libelle;

    /**
     * @ORM\Column(type="money")
     * @Assert\NotBlank(message = "Prix obligatoire")
     * @Assert\Range(min = 0.1, max = 999)
     */
    private $prix;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTime")
     */
    private $dateCreation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categorie", inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Recette", mappedBy="produits")
     */
    private $recettes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message = "Description obligatoire")
     * @Assert\Length(
     *     min = 15,
     *     max = 255,
     *     minMessage = "Description trop courte",
     *     maxMessage = "Description trop longue"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cru;

    /**
     * @ORM\Column(type="boolean")
     */
    private $cuit;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bio;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Type("\DateTime")
     */
    private $debutDisponibilite;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Type("\DateTime")
     * @Assert\Range(minPropertyPath="debutDisponibilite")
     */
    private $finDisponibilite;

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

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function __construct()
    {
        $this->dateCreation = new \DateTime('now');
        $this->recettes = new ArrayCollection();
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection|Recette[]
     */
    public function getRecettes(): Collection
    {
        return $this->recettes;
    }

    public function addRecette(Recette $recette): self
    {
        if (!$this->recettes->contains($recette)) {
            $this->recettes[] = $recette;
            $recette->addProduit($this);
        }

        return $this;
    }

    public function removeRecette(Recette $recette): self
    {
        if ($this->recettes->contains($recette)) {
            $this->recettes->removeElement($recette);
            $recette->removeProduit($this);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCru(): ?bool
    {
        return $this->cru;
    }

    public function setCru(bool $cru): self
    {
        $this->cru = $cru;

        return $this;
    }

    public function getCuit(): ?bool
    {
        return $this->cuit;
    }

    public function setCuit(bool $cuit): self
    {
        $this->cuit = $cuit;

        return $this;
    }

    public function getBio(): ?bool
    {
        return $this->bio;
    }

    public function setBio(bool $bio): self
    {
        $this->bio = $bio;

        return $this;
    }

    public function getDebutDisponibilite(): ?\DateTimeInterface
    {
        return $this->debutDisponibilite;
    }

    public function setDebutDisponibilite(?\DateTimeInterface $debutDisponibilite): self
    {
        $this->debutDisponibilite = $debutDisponibilite;

        return $this;
    }

    public function getFinDisponibilite(): ?\DateTimeInterface
    {
        return $this->finDisponibilite;
    }

    public function setFinDisponibilite(?\DateTimeInterface $finDisponibilite): self
    {
        $this->finDisponibilite = $finDisponibilite;

        return $this;
    }
}
