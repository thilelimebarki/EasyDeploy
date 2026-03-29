<?php

namespace App\Entity;

use App\Repository\TechnicienRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
class Technicien implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $motDePasse = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): static
    {
        $this->motDePasse = $motDePasse;
        return $this;
    }

    // UserInterface & PasswordAuthenticatedUserInterface
    public function getPassword(): string
    {
        return $this->motDePasse;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials()
    {
        // Rien à effacer pour l'instant
    }

    #[ORM\Column(type: 'boolean')]
private bool $isVerified = true; // ✅ important : par défaut TRUE pour ne pas bloquer les anciens

#[ORM\Column(length: 64, nullable: true)]
private ?string $verificationToken = null;

public function isVerified(): bool
{
    return $this->isVerified;
}

public function setIsVerified(bool $isVerified): self
{
    $this->isVerified = $isVerified;
    return $this;
}

public function getVerificationToken(): ?string
{
    return $this->verificationToken;
}

public function setVerificationToken(?string $verificationToken): self
{
    $this->verificationToken = $verificationToken;
    return $this;
}
}
