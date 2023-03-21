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
            $categories = $this->marketServices->getCategories();
        return $this->render('products/index.html.twig', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/products/{title}-{id}', name: 'app_products_show-product')]
    public function showProduct($title,$id): Response
    {


            $product = $this->marketServices->getProduct($id);

        return $this->render('products/show.html.twig', [
            'product' => $product,
        ]);
    }
}
