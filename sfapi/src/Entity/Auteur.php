<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;



/**
 * @ApiResource(
 *     collectionOperations={"get","post"},
 *      itemOperations={"get","put","patch"},
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
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"auteurs:read","auteurs:write"})
     */
    private $createAdt;

    /**
     * Biographie text html
     * @Groups({"auteurs:read"})
     */
    private $biographie;


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
    public function getCreatedAdtAgo() : string{

        return Carbon::instance($this->getCreateAdt())->diffForHumans();
    }

    public function __construct(){

        $this->createAdt = new \DateTimeImmutable();
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
     */
    public function setTextBiographie(?string $biographie): self
    {
        $this->biographie = nl2br($biographie);

        return $this;
    }



}
