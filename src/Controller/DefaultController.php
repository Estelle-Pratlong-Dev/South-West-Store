<?php

namespace App\Controller;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /***************************************************************
                    FONCTION VERS PAGE ACCUEIL
     ***************************************************************/
    /**
     * @Route("/default", name="default")
     */
    public function index()
    {
        return $this->render('default/index.html.twig', []);
    }
    /***************************************************************
                    FONCTION VERS PAGE MENTIONS LEGALES
     ***************************************************************/
    public function mentions()
    {
        return $this->render('default/mentions.html.twig', []);
    }
    /***************************************************************
                    FONCTION VERS PAGE CGV
     ***************************************************************/
    public function cgv()
    {
        return $this->render('default/cgv.html.twig', []);
    }
    /***************************************************************
                    FONCTION VERS PAGE LE SHOP
     ***************************************************************/
    public function aPropos()
    {
        return $this->render('default/a_propos.html.twig', []);
    }
}
