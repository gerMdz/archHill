<?php

namespace App\Controller;

use App\Services\MarketServices;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    private MarketServices $marketServices;

    /**
     * @param MarketServices $marketServices
     */
    public function __construct(MarketServices $marketServices)
    {
        $this->marketServices = $marketServices;
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/products', name: 'app_products')]
    public function index(): Response
    {
            $products = $this->marketServices->getProducts();
        return $this->render('products/index.html.twig', [
            'products' => $products
        ]);
    }
}
