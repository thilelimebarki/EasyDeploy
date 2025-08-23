<?php

namespace App\Controller;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    #[Route('/applications', name: 'app_application_index')]
    public function index(ApplicationRepository $applicationRepository): Response
    {
        $applications = $applicationRepository->findAll();

        return $this->render('application/index.html.twig', [
            'applications' => $applications
        ]);
    }

        #[Route('/new', name: 'app_application_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $app = new Application();
        $form = $this->createForm(ApplicationType::class, $app);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($app);
            $em->flush();
            return $this->redirectToRoute('app_application_index');
        }

        return $this->render('application/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route('/applications/install/{id}', name: 'app_application_install')]
public function install(Application $application): Response
{
    // Contenu du fichier .bat généré à la volée
    $batContent = "@echo off\r\n";
    $batContent .= "PowerShell -ExecutionPolicy Bypass -File \"%~dp0" . $application->getScriptPath() . "\"\r\n";
    $batContent .= "pause\r\n";

    // Nom du fichier
    $fileName = 'install_' . strtolower(str_replace(' ', '_', $application->getNomApplication())) . '.bat';

    // Crée le fichier temporaire
    $tempFile = tempnam(sys_get_temp_dir(), 'bat');
    file_put_contents($tempFile, $batContent);

    // Envoi du fichier au navigateur
    return $this->file($tempFile, $fileName, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
}

#[Route('/scripts/{filename}', name: 'app_script_download')]
public function downloadScript(string $filename): Response
{
    $scriptPath = $this->getParameter('kernel.project_dir') . '/public/uploads/scripts/' . $filename;

    if (!file_exists($scriptPath)) {
        throw $this->createNotFoundException('Le script demandé est introuvable.');
    }

    return $this->file($scriptPath);
}

    // Route pour afficher le tableau de sélection
#[Route('/applications/select-edit', name: 'app_application_select_edit')]
public function selectEdit(EntityManagerInterface $em): Response
{
    $applications = $em->getRepository(Application::class)->findAll();

    return $this->render('application/select_edit.html.twig', [
        'applications' => $applications
    ]);
}

// Route pour modifier une application précise
#[Route('/applications/{id}/edit', name: 'app_application_edit')]
public function edit(Request $request, Application $application, EntityManagerInterface $em): Response
{
    $form = $this->createForm(ApplicationType::class, $application);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->flush();
        return $this->redirectToRoute('app_application_select_edit'); // retour à la liste
    }

    return $this->render('application/edit.html.twig', [
        'form' => $form->createView(),
        'application' => $application
    ]);
}


#[Route('/applications/select-delete', name: 'app_application_select_delete')]
public function selectDelete(EntityManagerInterface $em): Response
{
    $applications = $em->getRepository(Application::class)->findAll();

    return $this->render('application/select_delete.html.twig', [
        'applications' => $applications,
    ]);
}

#[Route('/applications/{id}', name: 'app_application_delete', methods: ['POST'])]
public function delete(Request $request, Application $application, EntityManagerInterface $em): Response
{
    if ($this->isCsrfTokenValid('delete' . $application->getId(), $request->request->get('_token'))) {
        $em->remove($application);
        $em->flush();
    }

    return $this->redirectToRoute('app_application_index');
}


}
