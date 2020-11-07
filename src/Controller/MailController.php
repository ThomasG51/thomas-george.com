<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class MailController extends AbstractController {

    /**
     * @Route("/email", name="email")
     * @param MailerInterface $mailer
     * @param Request $request
     */
    public function sendEmail(MailerInterface $mailer, Request $request) {
        $form_firstname = htmlspecialchars($request->get('firstname'));
        $form_lastname = htmlspecialchars($request->get('lastname'));
        $form_email = htmlspecialchars($request->get('email'));
        $form_content = htmlspecialchars($request->get('content'));
        $form_check = $request->get('check-form');
        $no_spam = $request->get('nospam');

        if(!empty($no_spam)){
            $this->addFlash(
                'email_failed',
                'Failed : No spam please !'
            );
            return $this->redirectToRoute('home');
        }

        if ($form_check != null && !empty($form_email)  && !empty($form_firstname)  && !empty($form_lastname)  && !empty
                ($form_content)){
            $this->addFlash(
                'email_sent',
                'Votre message a bien été envoyé!'
            );
            
            $email = (new Email())
                ->from($form_email)
                ->to('contact@thomas-george.com')
                ->replyTo($form_email)
                ->subject('TG : ' . $form_firstname . ' ' . $form_lastname)
                ->text($form_content)
                ->html('
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <style>
                        * {
                            margin: 0;
                            padding: 0;
                            box-sizing: border-box;
                        }
                        </style>
                    </head>
                    <body style="background-color: #100E17; color: #ffffff; width: 100vw;" class="d-flex flex-row align-items-center">
                        <div style="background-color: #8DC246; color: #ffffff; width: 100vw">
                            <h1 style="text-align: center; padding-top: 20px; padding-bottom: 20px;">Vous avez reçu un message!</h1>
                        </div>
                    
                        <div style="width: 80vw; margin: auto; margin-top: 60px">
                            <h4 style="margin-bottom: 8px;">De: ' . $form_firstname . ' ' . $form_lastname . '</h4>
                            <h4>Contact: ' . $form_email . '</h4>

                            <h4 style="margin-top: 40px; margin-bottom: 8px;">Message:</h4>
                            <p style="text-align: justify;">' . $form_content . '</p>
                        </div>
                    
                        <div style="position: fixed; bottom: 0; left: 10vw; width: 80vw; border-top: 1px solid #8DC246;">
                            <p style="text-align: center; margin-top: 20px; margin-bottom: 20px;">Copyright &copy; 2020 thomas-george.com</p>
                        </div>
                    </body>
                    </html>
                ');

            $mailer->send($email);
        }else {
            $this->addFlash(
                'email_failed',
                'Echec : Veuillez verifier vos informations'
            );
        }

        return $this->redirectToRoute('home');
    }
}