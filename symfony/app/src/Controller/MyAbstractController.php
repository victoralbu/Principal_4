<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MyAbstractController extends AbstractController
{
    protected function isLoggedIn(){
        return $this->getUser() !== null;
    }
}