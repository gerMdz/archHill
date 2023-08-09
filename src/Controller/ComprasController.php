<?php

namespace App\Controller;

use App\Services\MarketServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ComprasController extends AbstractController
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
     * Compras de un usuario
     * @param Request $request
     * @return Response
     */
    #[Route('/compras', name: 'app_compras')]
    public function index(Request $request): Response
    {
        return $this->render('compras/index.html.twig', [
            'controller_name' => 'ComprasController',
        ]);
    }

    /**
     * Productos de un usuario
     * @param Request $request
     * @return Response
     */
    #[Route('/compras/productos', name: 'app_compras_productos')]
    public function compraProductos(Request $request): Response
    {
        return $this->render('compras/index.html.twig', [
            'controller_name' => 'ComprasController',
        ]);
    }
}
