<?php

namespace App\Controller;

use App\Services\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

#[Route('/products', name: 'app_products_', methods: ['GET'])]
class ProductController extends AbstractController
{
    #[Route('', name: 'list')]
    public function list(
        Request $request,
        ProductRepository $productRepository,
        ProductService $productService,
    ): JsonResponse {
        try {
            $productFilter = $productService->transformParamArrayToObject($request->query->all());
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $products = $productRepository->findAllFiltered($productFilter);
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        if (empty($products)) {
            return $this->json([
                'error' => 'No products found',
            ], Response::HTTP_NOT_FOUND);
        }

        $output = [];
        $output['items'] = $productService->transformProductsToArray($products, $productFilter);

        return $this->json($output, Response::HTTP_OK);
    }
}
