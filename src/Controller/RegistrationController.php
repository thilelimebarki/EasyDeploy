<?php
// src/Controller/RegistrationController.php

namespace App\Controller;

use App\Entity\Technicien;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        $technicien = new Technicien();
        $form = $this->createForm(RegistrationFormType::class, $technicien); // Création du formulaire d'inscription basé sur l'entité Technicien
        $form->handleRequest($request); // Analyse les données reçues dans la requête HTTP (POST)

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('motDePasse')->getData(); // Récupère le mot de passe en clair envoyé par l'utilisateur
            $hashedPassword = $hasher->hashPassword($technicien, $plainPassword); // Hash le mot de passe (sécurisation)
            $technicien->setMotDePasse($hashedPassword); // Enregistre le mot de passe hashé dans l’entité
            $em->persist($technicien); // Prépare l’entité à être stockée en base
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
