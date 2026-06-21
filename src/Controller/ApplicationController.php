<?php

namespace App\Controller;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Contient les données envoyées
use Symfony\Component\HttpFoundation\Response; // Réponse HTTP
use Symfony\Component\HttpFoundation\ResponseHeaderBag; // Gestion du téléchargement de fichiers
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException; // Gestion des erreurs d’upload
use Symfony\Component\HttpFoundation\File\UploadedFile; // Représente un fichier uploadé
use Symfony\Component\String\Slugger\SluggerInterface;// Sert à sécuriser les noms de fichiers
use Symfony\Component\HttpFoundation\BinaryFileResponse; // Réponse pour télécharger un fichier
use App\Entity\Installation; 


class ApplicationController extends AbstractController
{
    #[Route('/applications', name: 'app_application_index')]
    public function index(ApplicationRepository $applicationRepository): Response
    {
        $applications = $applicationRepository->findAll(); // Récupère toutes les applications depuis la base

        return $this->render('application/index.html.twig', [ // Envoie la liste à la vue Twig
            'applications' => $applications
        ]);
    }

    // Route pour afficher le tableau de sélection
#[Route('/applications/select-edit', name: 'app_application_select_edit')]
public function selectEdit(EntityManagerInterface $em): Response
{
    // Récupère toutes les applications pour les afficher dans une liste
    $applications = $em->getRepository(Application::class)->findAll();

    return $this->render('application/select_edit.html.twig', [
        'applications' => $applications
    ]);
}

// Route pour modifier une application précise
#[Route('/applications/{id}/edit', name: 'app_application_edit')]
public function edit(Request $request, Application $application, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    // Crée le formulaire à partir de l'entité Application
    $form = $this->createForm(ApplicationType::class, $application, [
    'is_edit' => true
]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        /** @var UploadedFile $scriptFile */
        $scriptFile = $form->get('scriptFile')->getData();

        // Si un nouveau fichier a été envoyé
        if ($scriptFile) {
            $originalFilename = pathinfo($scriptFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.ps1';

            try {
                // Déplace le fichier dans le dossier défini (services.yaml)
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

        $em->flush(); // Sauvegarde les modifications

        $this->addFlash('success', 'Application mise à jour avec succès !');
        return $this->redirectToRoute('app_application_select_edit'); // retour à la liste
    }

    // Affiche le formulaire d'édition
    return $this->render('application/edit.html.twig', [
        'form' => $form->createView(),
        'application' => $application,
    ]);
}

// Route pour afficher le tableau de sélection pour suppression
#[Route('/applications/select-delete', name: 'app_application_select_delete')]
public function selectDelete(EntityManagerInterface $em): Response
{
    // Récupère toutes les applications
    $applications = $em->getRepository(Application::class)->findAll();

    return $this->render('application/select_delete.html.twig', [
        'applications' => $applications,
    ]);
}

// Route pour supprimer une application précise
#[Route('/applications/{id}', name: 'app_application_delete', methods: ['POST'])]
public function delete(Request $request, Application $application, EntityManagerInterface $em): Response
{
    // Vérifie la validité du token CSRF avant suppression
    if ($this->isCsrfTokenValid('delete' . $application->getId(), $request->request->get('_token'))) {
        $em->remove($application);
        $em->flush();
    }

    return $this->redirectToRoute('app_application_index'); // Retour à la liste principale
}

// TÉLÉCHARGEMENT D'UN SCRIPT (.ps1)
#[Route('/script/{filename}', name: 'app_script_download')]
public function download(string $filename): BinaryFileResponse
{
    // Chemin du fichier
    $filePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $filename;

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Le fichier demandé est introuvable.');
    }

    // Force le téléchargement
    $response = $this->file($filePath, $filename, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    
    // Définir le Content-Type pour PowerShell
    $response->headers->set('Content-Type', 'application/octet-stream');

    return $response;
}

// Route pour ajouter une application 
#[Route('/new', name: 'app_application_new')]
public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $application = new Application();
    $form = $this->createForm(ApplicationType::class, $application);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Gérer l'upload du fichier de script
        // Récupèrer le fichier
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
        // Persistance et enregistrement
        $entityManager->persist($application);
        $entityManager->flush();

        return $this->redirectToRoute('app_application_index');
    }

    // Affiche le formulaire d’ajout
    return $this->render('application/new.html.twig', [
        'form' => $form->createView(),
    ]);
}

//Route pour installer une application
#[Route('/install/{id}', name: 'app_application_install')]
public function install(Application $application, EntityManagerInterface $em): Response
{
    $script = $application->getScriptPath();

    if (!$script) {
        throw $this->createNotFoundException("Aucun script n'est associé à cette application.");
    }

    // Enregistrement dans l’historique
    $historique = new Installation();
    $historique->setDate(new \DateTime());
    $historique->setTechnicien($this->getUser()->getUserIdentifier()); // email ou login de l’utilisateur
    $historique->setNomPc(gethostname()); // récupère le nom du poste 
    $historique->setLogiciel($application->getNomApplication());

    try {
        // Chemin complet vers le script PowerShell
        $scriptUrl = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $script;

        // Contenu du .bat qui exécute le .ps1
        $batContent = '@echo off
    chcp 65001 >nul

powershell -ExecutionPolicy Bypass -File "' . $scriptUrl . '"
pause';

        // Génération dynamique du Nom et chemin complet du fichier .bat
        $batFilename = 'installer_' . uniqid() . '.bat';
        $batPath = $this->getParameter('kernel.project_dir') . '/public/temp/' . $batFilename;

        // Créer le dossier temp si pas encore existant
        if (!file_exists(dirname($batPath))) {
            mkdir(dirname($batPath), 0777, true);
        }

        // Enregistre le contenu dans le fichier .bat
        file_put_contents($batPath, $batContent);

        // Si tout va bien, mettre à jour le statut à "Installateur téléchargé"
        $historique->setStatut("Installateur téléchargé");
        $em->persist($historique);
        $em->flush();

        // Retourner le .bat à l'utilisateur pour téléchargement/exécution
        return $this->file($batPath, $batFilename, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    } catch (\Exception $e) {
        // En cas d'erreur, mettre le statut à "Téléchargement échoué"
        $historique->setStatut("Téléchargement échoué");
        $em->persist($historique);
        $em->flush();
        throw $e;
    }
}
}
