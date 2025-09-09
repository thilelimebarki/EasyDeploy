<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_application = null;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $scriptPath = null;



    // GETTERS & SETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomApplication(): ?string
    {
        return $this->nom_application;
    }

    public function setNomApplication(string $nom_application): self
    {
        $this->nom_application = $nom_application;
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
    public function getScriptPath(): ?string
    {
        return $this->scriptPath;
    }

    public function setScriptPath(?string $scriptPath): self
    {
        $this->scriptPath = $scriptPath;
        return $this;
    }

}