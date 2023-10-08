<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Products;


class ProductsController extends AbstractController
{

	public function __construct(private readonly EntityManagerInterface $em){

	} 

    #[Route('/products', name: 'app_products')]
    public function index(): Response
    {

		$products = $this->em->getRepository(Products::class)->findAll();


        return $this->render('products/index.html.twig', [
            'products' => $products,
        ]);
    }
}
