<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    public function homepageAction()
    {
        return $this->render('main/homepage.html.twig');
    }
}
