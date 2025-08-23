<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nomApplication = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $scriptPath = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $scriptCommand = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomApplication(): ?string
    {
        return $this->nomApplication;
    }

    public function setNomApplication(string $nomApplication): self
    {
        $this->nomApplication = $nomApplication;
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

    public function getScriptCommand(): ?string
    {
        return $this->scriptCommand;
    }

    public function setScriptCommand(?string $scriptCommand): self
    {
        $this->scriptCommand = $scriptCommand;
        return $this;
    }

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
private ?string $commandeExecution = null;

public function getCommandeExecution(): ?string
{
    return $this->commandeExecution;
}

public function setCommandeExecution(?string $commandeExecution): self
{
    $this->commandeExecution = $commandeExecution;
    return $this;
}
}
