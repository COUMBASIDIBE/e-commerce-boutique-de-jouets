<?php

namespace App\Controller\Front;

use App\Entity\User;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Form\Front\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setEnabled(false);
            $user->setConfirmationToken(random_bytes(24));
            $user->setLastLoginAt(new \DateTimeImmutable());

            $nom = $request->request->get('registration_form')['lastname'];
            $prenom = $request->request->get('registration_form')['firstname'];
            $to = $request->request->get('registration_form')['email'];


            $email = (new TemplatedEmail())
                ->from('Bjouets@example.com')
                ->to(new Address($to))
                ->subject('Confirmation Inscription!')

                ->htmlTemplate('Front/emailsignup.html.twig')
                ->context([
                    'expiration_date' => new \DateTime('+7 days'),
                    'nom' => $nom,
                    'prenom' => $prenom,
                ]);

            $mailer->send($email);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('Front/register.html.twig', [
            'formu' => $form->createView(),
        ]);
    }
}
