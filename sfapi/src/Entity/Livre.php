<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 */
class Livre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("liste_livres")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("liste_livres")
     */
    private $titre;

    /**
     * @ORM\Column(type="integer")
     * @Groups("liste_livres")
     */
    private $annee;

    /**
     * @ORM\ManyToOne(targetEntity=Auteur::class, inversedBy="livre")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("liste_livres")
     */
    private $auteur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): self
    {
        $this->annee = $annee;

        return $this;
    }

    public function getAuteur(): ?Auteur
    {
        return $this->auteur;
    }

    public function setAuteur(?Auteur $auteur): self
    {
        $this->auteur = $auteur;

        return $this;
    }
}
