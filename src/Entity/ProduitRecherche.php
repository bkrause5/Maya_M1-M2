<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

class ProduitRecherche
{
    /**
     * @var string|null
     */
    private $libelle;
    /**
     * @var float|null
     */
    private $prixMini;
    /**
     * @var float|null
     */
    private $prixMaxi;

    /**
     * @return string|null
     */
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * @param string|null $libelle
     */
    public function setLibelle(?string $libelle): void
    {
        $this->libelle = $libelle;
    }

    /**
     * @return float|null
     */
    public function getPrixMini(): ?float
    {
        return $this->prixMini;
    }

    /**
     * @param float|null $prixMini
     */
    public function setPrixMini(?float $prixMini): void
    {
        $this->prixMini = $prixMini;
    }

    /**
     * @return float|null
     */
    public function getPrixMaxi(): ?float
    {
        return $this->prixMaxi;
    }

    /**
     * @param float|null $prixMaxi
     */
    public function setPrixMaxi(?float $prixMaxi): void
    {
        $this->prixMaxi = $prixMaxi;
    }
}
