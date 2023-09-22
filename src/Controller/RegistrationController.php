<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Service\MailerService;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, MailerService $mailerService, TokenGeneratorInterface $tokenGeneratorInterface ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

			// TOKEN
			$tokenRegistration = $tokenGeneratorInterface->generateToken();

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

			// USER TOKEN
			$user->getTokenRegistration($tokenRegistration);
			

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

			// MAIL SEND

			$mailerService->send(
				$user->getEmail(),
				'Confirmation du compte ustilisateur',
				'registration_confirmation.html.twig',
				[
					'user' => $user,
					'token' => $tokenRegistration,
					'lifeTimeToken' => $user->getTokenRegistrationLifeTime()->format('d-m-Y H:i:s')
				]
				);

			$this->addFlash('Success', 'Votre compte a bien été crée. Veuillez vérifier vous email pour l\'activer');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }



    #[Route('/verify/{token}/id<\d+>', name: 'account_verify', methods: ['GET'])]
	public function verify (string $token , User $user, EntityManagerInterface $em) : Response{

		if($user->getTokenRegistration() != $token){
			throw new AccessDeniedException();
		}

		if($user->getTokenRegistration() === null){
			throw new AccessDeniedException();
		}

		if(new DateTime('now') > $user->getTokenRegistrationLifeTime()){
			throw new AccessDeniedException();
		}

		$user->setIsVerified(true);
		$user->setTokenRegistration(true);
		$em->flush();

		$this->addFlas('success', 'Votre compte a été activé, vous pouvez à présetn vous connecter');
		
		return $this->redirectToRoute('app_login');

	}
}
