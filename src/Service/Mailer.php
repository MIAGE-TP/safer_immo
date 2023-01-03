<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailConfirmation($mail, $token)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('info@safer.com', 'Safer Immobilier'))
            ->to($mail)
            ->subject('Confirmation de votre adresse mail')

            // path of the Twig template to render
            ->htmlTemplate('registration/confirmation_email.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'token' => $token,
            ])
        ;

        $this->mailer->send($email);
    }

    public function sendFavorites($user, $favs, $bodyRenderer)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('info@safer.com', 'Safer Immobilier'))
            ->to($user->getEmail())
            ->subject('Des biens qui pourraient vous interesser')

            // path of the Twig template to render
            ->htmlTemplate('mail/favs.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'user' => $user,
                'favs' => $favs
            ])
        ;

        $bodyRenderer->render($email);

        $this->mailer->send($email);
    }
}