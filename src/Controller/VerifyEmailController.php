<?php

namespace App\Controller;

use App\Repository\TechnicienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerifyEmailController extends AbstractController
{
    #[Route('/verify-email', name: 'app_verify_email', methods: ['GET'])]
    public function verify(
        Request $request,
        TechnicienRepository $repo,
        EntityManagerInterface $em
    ): Response {
        $token = $request->query->get('token');

        if (!$token) {
            $this->addFlash('error', 'Token manquant.');
            return $this->redirectToRoute('app_login');
        }

        $technicien = $repo->findOneBy(['verificationToken' => $token]);

        if (!$technicien) {
            $this->addFlash('error', 'Lien invalide ou expiré.');
            return $this->redirectToRoute('app_login');
        }

        $technicien->setIsVerified(true);
        $technicien->setVerificationToken(null);

        $em->flush();

        $this->addFlash('success', 'Compte validé ✅ Vous pouvez vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}