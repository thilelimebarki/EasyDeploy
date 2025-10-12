<?php

// src/Entity/HistoriqueInstallation.php
namespace App\Entity;

use App\Repository\InstallationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InstallationRepository::class)]
class Installation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date;

    #[ORM\Column(length: 255)]
    private string $technicien;

    #[ORM\Column(length: 255)]
    private string $nomPc;

    #[ORM\Column(length: 255)]
    private string $logiciel;

    #[ORM\Column(length: 50)]
    private string $statut;

    // Getters et setters classiques
    public function getId(): ?int { return $this->id; }

    public function getDate(): \DateTimeInterface { return $this->date; }
    public function setDate(\DateTimeInterface $date): self { $this->date = $date; return $this; }

    public function getTechnicien(): string { return $this->technicien; }
    public function setTechnicien(string $technicien): self { $this->technicien = $technicien; return $this; }

    public function getNomPc(): string { return $this->nomPc; }
    public function setNomPc(string $nomPc): self { $this->nomPc = $nomPc; return $this; }

    public function getLogiciel(): string { return $this->logiciel; }
    public function setLogiciel(string $logiciel): self { $this->logiciel = $logiciel; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): self { $this->statut = $statut; return $this; }
}

