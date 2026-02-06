<?php

declare(strict_types=1);

namespace App\Modules\Packaging\RemotePackager\API;

use App\Helpers\Json\Json;
use App\Modules\Core\BinPackagingConfig;
use App\Modules\Packaging\RemotePackager\Exceptions\ApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Uri;
use Throwable;

class PackagingApi
{
    private Client $client;

    public function __construct(
        private readonly BinPackagingConfig $config,
    ) {
        $this->client = new Client([
            'base_uri' => $config->baseUrl,
        ]);
    }

    /**
     * @param array<array{
     *     id: int,
     *     w: float,
     *     h: float,
     *     d: float,
     *     wg: float,
     *     q: int,
     *     vr: int,
     * }> $items
     * @param array<array{
     *     id: int,
     *     w: float,
     *     h: float,
     *     d: float,
     * }> $bins
     *
     * @return array{
     *     id: int,
     *     w: float,
     *     h: float,
     *     d: float,
     * }
     * @throws ApiException
     */
    public function callPackIntoMany(array $items, array $bins): array
    {
        $query = $this->prepareQueryParams([
            'items' => $items,
            'bins' => $bins,
        ]);
        $uri = (new Uri('packIntoMany?query=' . $query));

        try {
            $response = $this->getClient()->get($uri);
            return $this->validatePackIntoManyResponse($response->getBody()->getContents());
        } catch (GuzzleException $e) {
            throw ApiException::unknown($e->getMessage());
        } catch (Throwable $e) {
            // Critical log should be here
            // rethrowing as ApiException to avoid unexpected exceptions in PackagingService
            throw ApiException::unknown(previous: $e);
        }
    }

    /**
     * @return array{
     *     id: int,
     *     w: float,
     *     h: float,
     *     d: float,
     * }
     *
     * @throws ApiException
     */
    private function validatePackIntoManyResponse(string $data): array
    {
        $decoded = Json::decode($data);

        $dummyResult = [
            'id' => -1,
            'w' => 0,
            'h' => 0,
            'd' => 0,
        ];
        $response = $decoded['response']
            ?? throw ApiException::unknown(is_string($decoded['error']) ? $decoded['error'] : null);
        if (! is_array($response)) {
            throw ApiException::unexpectedFormat();
        }
        $binsPacked = is_array($response['bins_packed'])
            ? $response['bins_packed']
            : throw ApiException::unexpectedFormat();
        if (
            count($binsPacked) === 0
            || count($binsPacked) > 1
        ) {
            return $dummyResult;
        }

        $singlePackedBin = is_array($binsPacked[0]) ? $binsPacked[0] : throw ApiException::missingField('packed bin');
        $binData = is_array($singlePackedBin['bin_data'])
            ? $singlePackedBin['bin_data']
            : throw ApiException::missingField('bin_data');

        return [
            'id' => (is_numeric($binData['id']))
                ? intval($binData['id'])
                : throw ApiException::missingField('id'),
            'w' => (is_numeric($binData['w']))
                ? floatval($binData['w'])
                : throw ApiException::missingField('width'),
            'h' => (is_numeric($binData['h']))
                ? floatval($binData['h'])
                : throw ApiException::missingField('height'),
            'd' => (is_numeric($binData['d']))
                ? floatval($binData['d'])
                : throw ApiException::missingField('depth(length)'),
        ];
    }

    protected function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function prepareQueryParams(array $params): string
    {
        return Json::encode(array_merge(
            [
                'username' => $this->config->user,
                'api_key' => $this->config->apiKey,
            ],
            $params
        ));
    }
}
