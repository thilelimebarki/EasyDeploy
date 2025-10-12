<?php

namespace App\Controller;

use App\Entity\Installation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoriqueController extends AbstractController
{
    #[Route('/historique', name: 'app_historique')]
    public function index(EntityManagerInterface $em): Response
    {
        $historique = $em->getRepository(Installation::class)->findAll();

        return $this->render('historique/index.html.twig', [
            'historique' => $historique,
        ]);
    }
}
