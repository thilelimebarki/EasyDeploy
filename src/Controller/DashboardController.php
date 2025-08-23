<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Security;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(Security $security): Response
    {
        $technicien = $security->getUser();

        return $this->render('dashboard/index.html.twig', [
            'technicien' => $technicien,
        ]);
    }
}
