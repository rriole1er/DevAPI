<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;


/**
 * @ApiResource(
 *     itemOperations={
 *     "get"={
 *     "normalization_context"={"groups"={"livres:read","livres:item:get"}},
 *     },
 *     "delete"={},
 *     "put",
 *     "patch",
 *     },
 *   normalizationContext={"groups"={"livres:read"}},
 *     denormalizationContext={"groups"={"livres:write"}}
 * )
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 */
class Livre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"livres:read","livres:write","auteurs:item:get"})
     */
    private $titre;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"livres:read","livres:write","auteurs:item:get"})
     */
    private $annee;

    /**
     * @ORM\ManyToOne(targetEntity=Auteur::class, inversedBy="livres")
     * @ORM\JoinColumn (nullable=false)
     * @Groups({"livres:read","livres:write"})
     */
    private $auteur;

    /**
     * @ORM\Column(type="integer")
     */
    private $langue;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $isbn;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

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

    public function getLangue(): ?int
    {
        return $this->langue;
    }

    public function setLangue(int $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

}
