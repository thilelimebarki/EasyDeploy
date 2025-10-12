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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Entity\Installation;


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
public function edit(Request $request, Application $application, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ApplicationType::class, $application);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        /** @var UploadedFile $scriptFile */
        $scriptFile = $form->get('scriptFile')->getData();

        if ($scriptFile) {
            $originalFilename = pathinfo($scriptFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.ps1';

            try {
                $scriptFile->move(
                    $this->getParameter('scripts_directory'), // défini dans services.yaml
                    $newFilename
                );
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l’upload du script');
            }

            // Mise à jour du chemin dans l’entité
            $application->setScriptPath($newFilename);
        }

        $em->flush();

        $this->addFlash('success', 'Application mise à jour avec succès !');
        return $this->redirectToRoute('app_application_select_edit'); // retour à la liste
    }

    return $this->render('application/edit.html.twig', [
        'form' => $form->createView(),
        'application' => $application,
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








#[Route('/script/{filename}', name: 'app_script_download')]
public function download(string $filename): BinaryFileResponse
{
    $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Le fichier demandé est introuvable.');
    }

    $response = $this->file($filePath, $filename, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    
    // Définir le Content-Type pour PowerShell
    $response->headers->set('Content-Type', 'application/octet-stream');

    return $response;
}


#[Route('/new', name: 'app_application_new')]
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $application = new Application();
    $form = $this->createForm(ApplicationType::class, $application);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer l'upload du fichier de script
        $file = $form->get('scriptFile')->getData();
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            
            $originalExtension = $file->getClientOriginalExtension(); // récupère l’extension réelle
            $newFilename = $safeFilename.'-'.uniqid().'.'.$originalExtension;

            // Déplacer le fichier dans le dossier public/uploads
            $file->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads',
                $newFilename
            );

            // Enregistrer le chemin du fichier dans la base de données
            
            $application->setScriptPath($newFilename);
        }



        $entityManager->persist($application);
        $entityManager->flush();

        return $this->redirectToRoute('app_application_index');
    }

    return $this->render('application/new.html.twig', [
        'form' => $form->createView(),
    ]);
}



// #[Route('/applications/{id}/install', name: 'app_application_install')]
// public function install(Application $application): Response
// {
//     $scriptPath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $application->getScriptPath();

//     if (!file_exists($scriptPath)) {
//         throw $this->createNotFoundException('Script introuvable.');
//     }

//     // Création du .bat temporaire
//     $batPath = $this->getParameter('kernel.project_dir') . '/public/uploads/installer_' . $application->getId() . '.bat';
//     $batContent = "@echo off\n";
//     $batContent .= "powershell -ExecutionPolicy Bypass -File \"" . $scriptPath . "\"\n";
//     $batContent .= "pause\n";

//     file_put_contents($batPath, $batContent);

//     return $this->file($batPath, 'installer.bat', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
// }

#[Route('/applications/{id}/install', name: 'app_application_install')]
public function install(Application $application, EntityManagerInterface $em): Response
{
    $scriptPath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $application->getScriptPath();

    if (!file_exists($scriptPath)) {
        throw $this->createNotFoundException('Script introuvable.');
    }

    // 🔹 Enregistrement dans l’historique
    $historique = new Installation();
    $historique->setDate(new \DateTime());
    $historique->setTechnicien($this->getUser()->getUserIdentifier()); // email ou login de l’utilisateur
    $historique->setNomPc(gethostname()); // récupère le nom du poste serveur (pas du client)
    $historique->setLogiciel($application->getNomApplication());
    $historique->setStatut("Installateur téléchargé");

    $em->persist($historique);
    $em->flush();

    // Création du .bat temporaire
    $batPath = $this->getParameter('kernel.project_dir') . '/public/uploads/installer_' . $application->getId() . '.bat';
    $batContent = "@echo off\n";
    $batContent .= "powershell -ExecutionPolicy Bypass -File \"" . $scriptPath . "\"\n";
    $batContent .= "pause\n";

    file_put_contents($batPath, $batContent);

    return $this->file($batPath, 'installer.bat', ResponseHeaderBag::DISPOSITION_ATTACHMENT);
}

}
