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
        $form = $this->createForm(RegistrationFormType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('motDePasse')->getData();
            $hashedPassword = $hasher->hashPassword($technicien, $plainPassword);
            $technicien->setMotDePasse($hashedPassword);
            $em->persist($technicien);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
