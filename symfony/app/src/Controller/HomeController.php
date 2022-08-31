<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends MyAbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->isLoggedIn() ? $this->render('Home/home.twig') : $this->redirectToRoute('app_login');
    }

    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }
}