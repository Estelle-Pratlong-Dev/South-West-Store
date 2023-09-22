<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;


class MailerService {

	public function __construct(private readonly MailerInterface $mailer){

	} 

	/*
	* Service qui permet de générer les mails
	*/
	public function send(
		string $to,
		string $subject,
		string $templateTwig,
		array $context ):void {
		
			$email = (new TemplatedEmail())
				->from(new Address('pratlong.estelle.test@gmail.com', 'South-West-Store'))
				->to($to)
				->subject($subject)
				->htmlTemplate("mails/$templateTwig")
				->context($context);
			
			try{
				$this->mailer->send($email);
			} catch(TransportExceptionInterface $transportException){
				throw $transportException;
			}

	}
}