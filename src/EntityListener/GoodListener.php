<?php

namespace App\EntityListener;

use App\Entity\OfferGalery;
use App\Entity\Contact;
use App\Entity\Good;
use App\Service\Mailer;
use Symfony\Component\Mime\BodyRendererInterface;
use Doctrine\ORM\EntityManagerInterface;


class GoodListener
{
    private BodyRendererInterface $body;
    private Mailer $mailer;
    private EntityManagerInterface $manager;

    public function __construct(BodyRendererInterface $bodyRenderer, Mailer $mailer, EntityManagerInterface $manager)
    {
        $this->body = $bodyRenderer;
        $this->mailer = $mailer;
        $this->manager = $manager;
    }

    public function postPersist(Good $good)
    {

        $contacts = $this->manager->getRepository(Contact::class)->findAll();

        foreach ($contacts as $prosper) {
            if ($good->getGoodcategory()->getId() == $prosper->getCategory()->getId()) {
                $this->mailer->sendPropser($prosper, $good, $this->body);
            }
        }
    }
}

