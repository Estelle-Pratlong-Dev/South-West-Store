<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Respect\Validation\Validator as v;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;

/* import des classes de PHPMailer */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class UserController extends AbstractController
{

    /***************************************************************
             FONCTION REDIRECTION ACCUEIL A LA CONNECTION
     ***************************************************************/
    public function log(){

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->redirectToRoute('homepage');
    }

    /***************************************************************
                        FONCTION PAGE SESSION USER
     ***************************************************************/

    /** 
     * @Route("/user", name="user")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }

    /***************************************************************
                        FONCTION EDIT USER PROFILE
     ***************************************************************/

    public function userEditProfile()
    {
        $msg = '';
        $errors = '';

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!empty($_POST)) {

            $safe = array_map('trim', array_map('strip_tags', $_POST));

            $em = $this->getDoctrine()->getManager();
            $usersFound = $em->getRepository(Users::class)->findBy(['email' => $safe['email']]);

            $errors = [
                (!v::length(2, 50)->validate($safe['name'])) ? 'Le nom doit comporter entre 2 et 50 caractères ' : null,
                (v::numeric()->validate($safe['name'])) ? 'Le nom ne peut être numérique ' : null,
                (!v::length(2, 50)->validate($safe['firstname'])) ? 'Le prénom doit comporter entre 2 et 50 caractères ' : null,
                (v::numeric()->validate($safe['firstname'])) ? 'Le prénom ne peut être numérique ' : null,
                (!v::length(5, 120)->validate($safe['address'])) ? 'L\'adresse est invalide ' : null,
                (!v::postalCode('FR')->validate($safe['pC'])) ? 'Code postal invalide ' : null,
                (!v::length(2, 120)->validate($safe['city'])) ? 'La ville n\'est pas valide ' : null,
                (v::numeric()->validate($safe['city'])) ? 'La ville n\'est pas valide ' : null,
                (!v::phone()->validate($safe['phone'])) ? 'Le numéro de téléphone n\'est pas valide ' : null,
                (!v::email()->validate($safe['email'])) ? 'L\'email n\'est pas valide ' : null,
            ];

            $errors = array_filter($errors);

            if (count($errors) == 0) {

                $user->setLastName($safe['name'])
                    ->setFirstName($safe['firstname'])
                    ->setAddress($safe['address'])
                    ->setAdditionalAddress($safe['complement'])
                    ->setPostalCode($safe['pC'])
                    ->setCity($safe['city'])
                    ->setPhoneNumber($safe['phone'])
                    ->setEmail($safe['email']);

                $em->flush();

                $msg = 'La modification à bien été prise en compte';
            }
        }

        return $this->render('user/edit_profile.html.twig', [
            'user' => $user,
            'msg'  => $msg,
            'errors'  => $errors,
        ]);
    }

    /***************************************************************
                         FONCTION SESSION INSCRIPTION
     ***************************************************************/

    public function deleteProfile($id)
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository(Users::class)->deleteUsers($id);

        return $this->redirectToRoute('homepage');
    }

    /***************************************************************
                         FONCTION SESSION INSCRIPTION
     ***************************************************************/
    /**
     * @Route("/register", name="app_register")
     */
    public function addUser()
    {
        $errors = '';
        $msg = '';


        if (!empty($_POST)) {
            $safe = array_map('trim', array_map('strip_tags', $_POST));
            $em = $this->getDoctrine()->getManager();
            $usersFound = $em->getRepository(Users::class)->findBy(['email' => $safe['email']]);

            $errors = [
                (!v::length(2, 50)->validate($safe['name'])) ? 'Le nom doit comporter entre 2 et 50 caractères ' : null,
                (v::numeric()->validate($safe['name'])) ? 'Le nom ne peut être numérique ' : null,
                (!v::length(2, 50)->validate($safe['firstname'])) ? 'Le prénom doit comporter entre 2 et 50 caractères ' : null,
                (v::numeric()->validate($safe['firstname'])) ? 'Le prénom ne peut être numérique ' : null,
                (!v::length(5, 120)->validate($safe['address'])) ? 'L\'adresse est invalide ' : null,
                (!v::postalCode('FR')->validate($safe['pC'])) ? 'Code postal invalide ' : null,
                (!v::length(2, 120)->validate($safe['city'])) ? 'La ville n\'est pas valide ' : null,
                (v::numeric()->validate($safe['city'])) ? 'La ville n\'est pas valide ' : null,
                (!v::phone()->validate($safe['phone'])) ? 'Le numéro de téléphone n\'est pas valide ' : null,
                (!v::email()->validate($safe['email'])) ? 'L\'email n\'est pas valide ' : null,
                (v::notEmpty()->validate($usersFound)) ? 'Cet email existe déjà' : null,
                (!v::notEmpty()->length(8, null)->regex('/[a-z]+/')->regex('/[A-Z]+/')->regex('/\d+/')->validate($safe['pwd'])) ? 'Le mot de passe doit comporter 1 maj, 1 chiffre, 8 caractères min' : null,
                (!v::equals($safe['pwd'])->validate($safe['pwdVerif'])) ? 'Les mots de passe ne sont pas identiques' : null,
            ];

            $errors = array_filter($errors);


            if (count($errors) == 0) {
                $hash = password_hash($safe['pwd'], PASSWORD_DEFAULT);

                $entityManager = $this->getDoctrine()->getManager();

                $user = new Users();
                $user->setLastName($safe['name']);
                $user->setFirstName($safe['firstname']);
                $user->setAddress($safe['address']);
                $user->setAdditionalAddress($safe['complement']);
                $user->setPostalCode($safe['pC']);
                $user->setCity($safe['city']);
                $user->setPhoneNumber($safe['phone']);
                $user->setEmail($safe['email']);
                $user->setPassword($hash);
                $user->setRoles(['ROLE_USER']);

                $entityManager->persist($user);
                // insert in BDD
                $entityManager->flush();

                // paramétrage et envoi de la confirmation via PHP MAILER 
                $mail = new PHPMailer;
                /* paramétrage du SMTP */
                $mail->SMTPOptions = [
                    'ssl' =>
                    [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                $mail->CharSet = 'UTF-8';
                $mail->isSMTP(); //connexion directe à un serveur SMTP
                $mail->isHTML(true); //mail au format HTML
                $mail->Host = 'smtp.gmail.com'; //serveur SMTP
                $mail->SMTPAuth = true; //serveur sécurisé
                $mail->Port = 465; //port utilisé par le serveur
                $mail->SMTPSecure = 'ssl'; //certificat SSL
                $mail->Username = 'pratlong.estelle.test@gmail.com'; //login
                $mail->Password = 'jerestelle@18'; //mot de passe
                $mail->AddAddress($safe['email']); //destinataire
                $mail->SetFrom('pratlong.estelle.test@gmail.com'); //expediteur
                $mail->Subject = 'Inscription South-West-Store'; //sujet
                // le corps du mail au format HTML
                $mail->Body = '<h1>Bienvenue ' . $safe['firstname'] . '</h1>
                    <p>Toute l\'equipe vous souhaite la bienvenue chez South-West-Store</p>
                    <p>Vous pouvez dès maintenant vous connecter sur votre espace personnel.</p>
                    <p>A tres vite sur South-West-Store!</p>
                    </br>
                    <p>Ne pas repondre</p>';

                // envoi du message
                if ($mail->Send()) {
                    $msg = 'Votre inscription est validé, bienvenue chez South-West-Store. Un mail de confirmation vient de vous être envoyé ';
                }
            } // count($errors)         
        } // if !empty($_POST)

        return $this->render('user/register.html.twig', [
            'msg'        =>   $msg,
            'erreur'     =>   $errors,
            'donnees'    =>   $safe ?? [],
        ]);
    } // fonction addUser

}
