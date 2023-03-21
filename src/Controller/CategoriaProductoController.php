<?php

namespace App\Controller;

use App\Services\MarketServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriaProductoController extends AbstractController
{

    private MarketServices $marketServices;

    /**
     * @param MarketServices $marketServices
     */
    public function __construct(MarketServices $marketServices)
    {
        $this->marketServices = $marketServices;
    }
    #[Route('/categoriaproducto/{title}-{id}/producto', name: 'app_categoria_producto')]
    public function index($title, $id): Response
    {
        $products = $this->marketServices->getCategoryProducts($id);

        return $this->render('categoria_producto/index.html.twig', [
            'products' => $products,
        ]);
    }
}
