<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ApiResource(
 *     collectionOperations={"get","post"},
 *      *     itemOperations={
 *     "get"={
 *     "normalization_context"={"groups"={"auteurs:read","auteurs:item:get"}},
 *     },
 *     "delete"={}
 *     },
 *      shortName="authors",
 *      normalizationContext={"groups"={"auteurs:read"}},
 *     denormalizationContext={"groups"={"auteurs:write"}}
 * )
 * @ORM\Entity(repositoryClass=AuteurRepository::class)
 */
class Auteur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"auteurs:read","auteurs:write","livres:item:get","livres:write"})
     * @Assert\NotBlank(
     *     message = "Le nom de l'auteur, ne peut pas être nul comme Jarod"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"auteurs:read","auteurs:write","livres:item:get"})
     * @Assert\NotBlank(
     *     message = "Le prenom de l'auteur, ne peut pas être nul comme Jarod"
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $createAdt;

    /**
     * Biographie text html
     * @ORM\Column(type="string", length=2000, nullable=true)
     * @Groups({"auteurs:read"})
     * @Assert\NotBlank(
     *     message = "La biographie de l'auteur, ne peut pas être nul comme Jarod"
     * )
     * @Assert\Length(
     *     min = 10,
     *     max = 2000,
     *     maxMessage="La biographie est trop longue -2000 car"
     * )
     */
    private $biographie;

    /**
     * @ORM\OneToMany(targetEntity=Livre::class, mappedBy="auteur", orphanRemoval=true)
     * @Groups({"auteurs:read"})
     */
    private $livres;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }


    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    public function getPrenom(): ?string
    {
        return $this->prenom;
    }



    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }



    public function getCreateAdt(): ?\DateTimeImmutable
    {
        return $this->createAdt;
    }

    /**
     * Retournes la date de création sous un format lisible
     * @Groups({"auteurs:read"})
     * @return string
     */
    public function getCreatedAdtAgo() : ?string{

        if($this->getCreateAdt())
            return Carbon::instance($this->getCreateAdt())->diffForHumans();
        return null;
    }

    public function __construct(){

        $this->createAdt = new \DateTimeImmutable();
        $this->livres = new ArrayCollection();
    }

    /**
     * @param \DateTimeImmutable $createAdt
     */
    public function setCreateAdt($createAdt)
    {
        $this->createAdt = $createAdt;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;

        return $this;
    }
    /**
     * Biographie text html
     * @Groups({"auteurs:write"})
     * @SerializedName ("biographie")
     */
    public function setTextBiographie(?string $biographie): self
    {
        $this->biographie = nl2br($biographie);

        return $this;
    }

    /**
     * @return Collection<int, Livre>
     */
    public function getLivres(): Collection
    {
        return $this->livres;
    }

    public function addLivre(Livre $livre): self
    {
        if (!$this->livres->contains($livre)) {
            $this->livres[] = $livre;
            $livre->setAuteur($this);
        }

        return $this;
    }

    public function removeLivre(Livre $livre): self
    {
        if ($this->livres->removeElement($livre)) {
            // set the owning side to null (unless already changed)
            if ($livre->getAuteur() === $this) {
                $livre->setAuteur(null);
            }
        }

        return $this;
    }



}
