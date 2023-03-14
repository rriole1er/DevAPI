<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Annotation\ApiFilter;


/**
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 * @ApiResource(
 *     itemOperations={
 *     "get"={
 *     "normalization_context"={"groups"={"livres:read","livres:item:get"}},
 *     },
 *     "delete"={},
 *     "put" = {},
 *     "patch" = {}
 *     },
 *   normalizationContext={"groups"={"livres:read"}},
 *     denormalizationContext={"groups"={"livres:write"}}
 * )
 *  * @ApiFilter (PropertyFilter::class, properties={
 *     "titre" : "partial",
 *     "auteur" : "exact",
 *     "auteur.nom" : "partial"
 * })
 * @ApiFilter(RangeFilter::class, properties={"annee"})
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
     * @Groups({"livres:read","livres:write","auteurs:read", "auteurs:write","auteurs:item:get"})
     */
    private $titre;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"livres:read","livres:write","auteurs:read", "auteurs:write","auteurs:item:get"})
     */
    private $annee;

    /**
     * @ORM\ManyToOne(targetEntity=Auteur::class, inversedBy="livres")
     * @ORM\JoinColumn (nullable=false)
     * @Groups({"livres:read","livres:write"})
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
