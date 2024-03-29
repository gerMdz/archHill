<?php

namespace App\Controller;

use App\Form\ProductsType;
use App\Form\RegistrationFormType;
use App\Services\MarketServices;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('products/index.html.twig', compact('products', 'categories'));
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/products/{title}-{id}', name: 'app_products_show-product')]
    public function showProduct($title, $id): Response
    {

        $product = $this->marketServices->getProduct($id);

        return $this->render('products/show.html.twig', compact('product'));
    }

    #[Route('/products/{title}-{id}/compra', name: 'app_products_compra-product')]
    public function compraProduct($title, $id): Response
    {
        $product = $this->marketServices->getProduct($id);

        return $this->render('products/show.html.twig', compact('product'));
    }

    /**
     * @throws GuzzleException
     */
    #[Route('/products/publicados', name: 'app_products_publicados-product')]
    public function publicadoProduct(Request $request): Response
    {

        $id = $request->get('id');
        $product = $this->marketServices->getProduct($id);


        return $this->render('products/show.html.twig', compact('product'));
    }

    #[Route('/products/publicados', name: 'app_products_publicados_post-product', methods: 'POST')]
    public function publicadoFormProduct($title, $id): Response
    {

        $product = $this->marketServices->getProduct($id);

        return $this->render('products/show.html.twig', compact('product'));
    }
    #[Route('/products/publicar', name: 'app_products_publicar')]
    public function publicarProducto(Request $request): Response
    {
//        $products = $this->marketServices->getCategoryProducts($id);
        $categories = $this->marketServices->getCategories();


        $form = $this->createForm(ProductsType::class);
//        $form->handleRequest($request);

        return $this->render('products/publicar.html.twig', [
            'categories' => $categories,
            'productForm' => $form->createView(),
        ]);
    }


}
