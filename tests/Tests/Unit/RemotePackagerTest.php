<?php

declare(strict_types=1);

namespace Tests\Tests\Unit;

use App\Helpers\Json\Json;
use App\Model\Box\Box;
use App\Model\Box\BoxRepository;
use App\Modules\Packaging\RemotePackager\API\PackagingApi;
use App\Modules\Packaging\RemotePackager\PackagingService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Tests\BaseTest;

class RemotePackagerTest extends BaseTest
{
    private MockHandler $clientMockHandler;

    private PackagingService $packagingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientMockHandler = new MockHandler([]);
        $handlerStack = HandlerStack::create($this->clientMockHandler);

        $guzzleClient = new Client([
            'handler' => $handlerStack,
        ]);

        $api = Mockery::mock(PackagingApi::class);
        $api->shouldAllowMockingProtectedMethods();
        $api->allows([
            "getClient" => $guzzleClient,
        ]);
        $boxRepository = $this->getServiceByType(BoxRepository::class);

        $this->packagingService = new PackagingService($boxRepository, $api);
    }

    protected function tearDown(): void
    {
        $preparedQueriesNumber = $this->clientMockHandler->count();
        self::assertSame(
            0,
            $preparedQueriesNumber,
            'Not all prepared mock API responses were used. Remaining: ' . $preparedQueriesNumber
        );

        parent::tearDown();
    }

    public function testPackager(): void
    {
        $boxes = $this->prepareBoxes();
        self::assertCount(5, $boxes);
    }

    private function prepareBoxes(): array
    {
        return [
            new Box(width: 2.5, height: 3.0, length: 1.0, maxWeight: 20.0, id: 1),
            new Box(width: 4.0, height: 4.0, length: 4.0, maxWeight: 20.0, id: 2),
            new Box(width: 2.0, height: 2.0, length: 10.0, maxWeight: 20.0, id: 3),
            new Box(width: 5.5, height: 6.0, length: 7.5, maxWeight: 30.0, id: 4),
            new Box(width: 9.0, height: 9.0, length: 9.0, maxWeight: 30.0, id: 5)
        ];
    }

    private function prepareApiResponseFromEntity(Box $box): void
    {
        $body = [
            'response' => [
                'bins_packed' => [
                    [
                        'bin_data' => [
                            'id' => $box->getId(),
                            'w' => $box->width,
                            'h' => $box->height,
                            'd' => $box->length,
                        ],
                    ],
                ],
            ],
        ];
        $this->prepareApiResponse(Json::encode($body));
    }

    private function prepareApiResponse(string $body): void
    {
        $this->clientMockHandler->append(
            new Response(
                200,
                [
                    'Content-Type' => 'application/json',
                ],
                $body
            )
        );
    }
}
