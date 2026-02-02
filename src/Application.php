<?php

declare(strict_types=1);

namespace App;

use App\Facades\PackagingFacade;
use App\Model\Product\ProductFacade;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final readonly class Application
{
    public function __construct(
        private PackagingFacade $packagingFacade,
        private ProductFacade $productFacade
    ) {
    }

    public function run(RequestInterface $request): ResponseInterface
    {
        $productList = $this->productFacade->getOrCreateProductsFromRequest(
            $request->getBody()->getContents()
        );

        return new Response(
            body: $this->packagingFacade->findBoxForProducts($productList)?->toJson()
                ?? 'Requested products cannot be packed into any available single box.',
        );
    }
}
