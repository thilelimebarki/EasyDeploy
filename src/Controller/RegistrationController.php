<?php

namespace App\Controller;

use App\Entity\Technicien;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        HttpClientInterface $client
    ): Response {
        $technicien = new Technicien();
        $form = $this->createForm(RegistrationFormType::class, $technicien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // 1) Password hash
            $plainPassword = $form->get('motDePasse')->getData();
            $technicien->setMotDePasse($hasher->hashPassword($technicien, $plainPassword));

            // 2) Nouveau compte = NON validé
            $technicien->setIsVerified(false);

            // 3) Token
            $token = bin2hex(random_bytes(32));
            $technicien->setVerificationToken($token);

            // 4) Save en BDD
            $em->persist($technicien);
            $em->flush();

            // 5) Lien de validation
            $baseUrl = $this->getParameter('app_base_url');
            $verifyUrl = $baseUrl . '/verify-email?token=' . $token;

            // 6) Mailjet API
            $apiKey = $this->getParameter('mailjet_api_key');
            $secretKey = $this->getParameter('mailjet_secret_key');
            $fromEmail = $this->getParameter('mail_from_email');
            $fromName = $this->getParameter('mail_from_name');

            $client->request('POST', 'https://api.mailjet.com/v3.1/send', [
                'auth_basic' => [$apiKey, $secretKey],
                'json' => [
                    'Messages' => [[
                        'From' => [
                            'Email' => $fromEmail,
                            'Name' => $fromName,
                        ],
                        'To' => [[
                            'Email' => $technicien->getEmail(),
                            'Name' => $technicien->getPrenom().' '.$technicien->getNom(),
                        ]],
                        'Subject' => 'Validez votre compte EasyDeploy',
                        'HTMLPart' => '
                            <p>Bonjour,</p>
                            <p>Merci de valider votre compte en cliquant sur ce lien :</p>
                            <p><a href="'.$verifyUrl.'">✅ Valider mon compte</a></p>
                            <p>Si vous n’êtes pas à l’origine de cette inscription, ignorez cet email.</p>
                        ',
                    ]],
                ],
            ]);

            $this->addFlash(
    'success',
    'Merci de bien vouloir vérifier votre adresse email et de valider votre compte.'
);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}