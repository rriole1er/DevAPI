<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Api\FilterInterface;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Controller\CreateLivrePublication;




/**
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 * @ApiResource(
 *     itemOperations={
 *     "get"={
 *     "normalization_context"={"groups"={"livres:read","livres:item:get"}},
 *     },
 *     "delete"={},
 *     "put" = {},
 *     "patch" = {},
 *     "publish" = {
 *          "method"="POST",
 *          "path"= "/livres/{id}/publish",
 *          "controller" = CreateLivrePublication::class,
 *          "read" = true,
 *          "validate"=true,
 *          "write" = true,
 *          "openapi_context" = {
 *              "summary": "Publier un livre",
 *              "requestBody":{
 *                  "content":{
 *                      "application/json":{
 *                              "schema":{},
 *                              "example": "{}"
 *                          }
 *                      }
 *                  },
 *                "parameters":{
 *                      {
 *                       "in":"path",
 *                      "name":"id",
 *                      "required":true,
 *                      "description":"Identifiant du livre"
 *                      }
 *                  }
 *             }
 *      }
 *     },
 *   normalizationContext={"groups"={"livres:read"}},
 *     denormalizationContext={"groups"={"livres:write"}}
 * )
 *  * @ApiFilter (SearchFilter::class, properties={
 *     "titre" : "partial",
 *     "auteur" : "exact",
 *     "auteur.nom" : "partial"
 * })
 * @ApiFilter (RangeFilter::class, properties={"annee"})
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

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     * @Groups({"lires:read","livres:item:get"})
     */
    private $isPublished = false;

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

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

}
