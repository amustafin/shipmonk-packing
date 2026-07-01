<?php

declare(strict_types=1);

namespace App;

use App\Model\Product\ProductFacade;
use App\Modules\Packaging\PackagingFacade;
use Doctrine\ORM\Exception\ORMException;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

final readonly class Application
{
    public function __construct(
        private PackagingFacade $packagingFacade,
        private ProductFacade $productFacade
    ) {
    }

    public function run(RequestInterface $request): ResponseInterface
    {
        try {
            $productList = $this->productFacade->getOrCreateProductsFromRequest(
                $request->getBody()->getContents()
            );
            return new Response(
                body: $this->packagingFacade->findBoxForProducts($productList)?->toJson()
                    ?? 'Requested products cannot be packed into any available single box.',
            );
        } catch (ORMException $e) {
            // Log error here to indicate database issues
            return new Response(status: 500, body: 'Internal Server Error: ' . $e->getMessage());
        } catch (Throwable) {
            // Log critical here to indicate unknown issues
            return new Response(status: 500, body: 'Internal Server Error');
        }
    }
}
