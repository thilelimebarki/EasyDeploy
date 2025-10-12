<?php

namespace App\Controller;

use App\Entity\Procedure;
use App\Form\ProcedureType;
use App\Repository\ProcedureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route('/procedure')]
class ProcedureController extends AbstractController
{
    #[Route('/', name: 'app_procedure_index', methods: ['GET'])]
    public function index(ProcedureRepository $procedureRepository): Response
    {
        return $this->render('procedure/index.html.twig', [
            'procedures' => $procedureRepository->findAll(),
        ]);
    }

#[Route('/procedure/new', name: 'app_procedure_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $procedure = new Procedure();
        $form = $this->createForm(ProcedureType::class, $procedure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $documentFile = $form->get('document')->getData();

            if ($documentFile) {
                $originalFilename = pathinfo($documentFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$documentFile->guessExtension();

                $documentFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads',
                    $newFilename
                );

                $procedure->setDocument($newFilename);
            }

            $em->persist($procedure);
            $em->flush();

            return $this->redirectToRoute('app_procedure_index');
        }

        return $this->render('procedure/new.html.twig', [
            'procedure' => $procedure,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_procedure_show', methods: ['GET'])]
    public function show(Procedure $procedure): Response
    {
        return $this->render('procedure/show.html.twig', [
            'procedure' => $procedure,
        ]);
    }

    #[Route('/{id}', name: 'app_procedure_delete', methods: ['POST'])]
    public function delete(Request $request, Procedure $procedure, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$procedure->getId(), $request->request->get('_token'))) {
            $entityManager->remove($procedure);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_procedure_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/procedure/select-delete', name: 'app_procedure_select_delete')]
public function selectDelete(EntityManagerInterface $em): Response
{
    $procedures = $em->getRepository(Procedure::class)->findAll();

    return $this->render('procedure/select_delete.html.twig', [
        'procedures' => $procedures,
    ]);
}

#[Route('/procedure/download/{filename}', name: 'app_procedure_download')]
public function download(string $filename): BinaryFileResponse
{
    $filePath = $this->getParameter('kernel.project_dir').'/public/uploads/'.$filename;

    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('Le fichier demandé est introuvable.');
    }

    return $this->file($filePath, $filename, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
}

#[Route('/procedure/select-edit', name: 'app_procedure_select_edit')]
public function selectEdit(EntityManagerInterface $em): Response
{
    $procedures = $em->getRepository(Procedure::class)->findAll();

    return $this->render('procedure/select_edit.html.twig', [
        'procedures' => $procedures,
    ]);
}

// #[Route('/procedure/{id}/edit', name: 'app_procedure_edit')]
// public function edit(Request $request, Procedure $procedure, EntityManagerInterface $em): Response
// {
//     $form = $this->createForm(ProcedureType::class, $procedure);
//     $form->handleRequest($request);

//     if ($form->isSubmitted() && $form->isValid()) {
//         $em->flush();
//         return $this->redirectToRoute('app_procedure_select_edit'); // retour à la liste
//     }

//     return $this->render('procedure/edit.html.twig', [
//         'form' => $form->createView(),
//         'procedure' => $procedure
//     ]);
// }

#[Route('/procedure/{id}/edit', name: 'app_procedure_edit')]
public function edit(Request $request, Procedure $procedure, EntityManagerInterface $em, SluggerInterface $slugger): Response
{
    $form = $this->createForm(ProcedureType::class, $procedure);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer le fichier uploadé
        $file = $form->get('document')->getData();

        if ($file) {
            // Créer un nom unique
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            // Déplacer le fichier
            $file->move(
                $this->getParameter('documents_directory'),
                $newFilename
            );

            // Associer le nouveau nom à l'entité
            $procedure->setDocument($newFilename);
        }

        $em->flush();
        return $this->redirectToRoute('app_procedure_select_edit');
    }

    return $this->render('procedure/edit.html.twig', [
        'form' => $form->createView(),
        'procedure' => $procedure,
    ]);
}


    
}
