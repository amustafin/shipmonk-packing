<?php

namespace App;

use App\Facades\PackagingFacade;
use App\Model\Product\ProductFacade;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final readonly class Application
{
    private PackagingFacade $packagingFacade;

    private ProductFacade $productFacade;

    public function __construct(EntityManager $entityManager)
    {
        $this->packagingFacade = PackagingFacade::create($entityManager);
        $this->productFacade = ProductFacade::create($entityManager);
    }

    public function run(RequestInterface $request): ResponseInterface
    {
        $productList = $this->productFacade->getOrCreateProductsFromRequest($request->getBody()->getContents());

        return new Response(
            body: $this->packagingFacade->findBoxForProducts($productList)?->toJson()
                ?? 'Requested products cannot be packed into any available single box.',
        );
    }
}
