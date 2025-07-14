<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Respect\Validation\Validator as v;
/* import des classes de PHPMailer */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



class ContactController extends AbstractController
{

	/***************************************************************
					FONCTION FORMULAIRE DE CONTACT
	 ***************************************************************/
	public function contactUs()
	{

		$errors = '';
		$msg = '';
		$success = '';
		if (!empty($_POST)) {
			$safe = array_map('trim', array_map('strip_tags', $_POST));
	// verif du formulaire de contact
			$errors = [
				(!v::length(2, 50)->validate($safe['name'])) ? 'Le nom doit comporter entre 2 et 50 caractères ' : null,
				(v::numeric()->validate($safe['name'])) ? 'Le nom ne peut être numérique ' : null,
				(!v::length(2, 50)->validate($safe['subject'])) ? 'Le sujet de votre message doit comporter entre 2 et 50 caractères ' : null,
				(v::numeric()->validate($safe['subject'])) ? 'Le sujet de votre message ne peut être numérique ' : null,
				(!v::email()->validate($safe['email'])) ? 'email non valide ' : null,
				(!v::length(20, 1000)->validate($safe['message'])) ? 'Votre message est trop court (ou trop long), 10 caractère minimum , 1 000 maximum <br> Essayer de commencer par <em>\'bonjour\'</em> ca vous fera deja gagner 7 caractères :)' : null,
			];

			$errors = array_filter($errors);

			if (count($errors) == 0) {

	// paramétrage et envoi via PHP MAILER 
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
				$mail->AddAddress('bbtuneur@hotmail.fr'); //destinataire
				$mail->SetFrom('pratlong.estelle.test@gmail.com'); //expediteur
				$mail->Subject = 'Message de ' . $safe['email']; //sujet
				// le corps du mail au format HTML
				$mail->Body = '	<h1>Message de ' . $safe['email'] . '</h1>
					<p>Nom: ' . $safe['name'] . '</p>
					<p>Sujet: ' . $safe['subject'] . '</p>
					<p>Message: ' . $safe['message'] . '</p>';

				// envoi du message
				if ($mail->Send()) {
					$success = 'Votre message à été envoyer, nous vous répondrons dès que possible ';
				} else $errors = 'Une erreur est survenue, veuillez réessayer plus tard ';
			} // if (count($errors) == 0) 
		} // if !empty($_POST)   	

		return $this->render('contact/contact.html.twig', [
			'controller_name' => 'ContactController',
			'erreur'     =>   $errors,
			'msg'        =>   $msg,
			'msg'        =>   $success,
			'donnees'    =>   $safe ?? [],
		]);
	} // function contactUs
}
