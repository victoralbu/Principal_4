<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends MyAbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $db): Response
    {
        $locations = $db->getRepository(Location::class)->findAll();
        return $this->isLoggedIn() ? $this->render('Home/home.twig', ['locations' => $locations]) : $this->redirectToRoute('app_login');
    }

    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }
}