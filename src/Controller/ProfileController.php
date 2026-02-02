<?php

namespace App\Controller;

use App\Entity\Technicien;
use App\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class ProfileController extends AbstractController
{
    #[Route('/profil', name: 'app_profile')]
public function index(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher
): Response
{
    /** @var Technicien $technicien */
    $technicien = $this->getUser();

    if (!$technicien) {
        throw $this->createAccessDeniedException();
    }

    $form = $this->createForm(ProfileType::class, $technicien);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        //  Gestion du mot de passe
        $plainPassword = $form->get('plainPassword')->getData();

        if (!empty($plainPassword)) {
            $hashedPassword = $passwordHasher->hashPassword(
                $technicien,
                $plainPassword
            );
            $technicien->setMotDePasse($hashedPassword);
        }

        $em->flush();

        $this->addFlash('success', 'Profil mis à jour avec succès');
        return $this->redirectToRoute('app_profile');
    }

    return $this->render('profile/index.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
