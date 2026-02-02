<?php

declare(strict_types=1);

namespace App\API;

use App\Helpers\JsonHelper;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use SensitiveParameter;

final readonly class PackagingApi
{
    public function __construct(
        private string $userName,
        #[SensitiveParameter]
        private string $apiKey,
        private Client $client,
    ) {
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
     */
    public function callPackIntoMany(array $items, array $bins): array
    {
        $query = $this->prepareQueryParams([
            'items' => $items,
            'bins' => $bins,
        ]);
        $uri = (new Uri('packIntoMany?query=' . $query));

        $response = $this->getClient()->get($uri);
        return $this->validatePackIntoManyResponse($response->getBody()->getContents());
    }

    /**
     * @return array{
     *     id: int,
     *     w: float,
     *     h: float,
     *     d: float,
     * }
     *
     * @throws Exception
     */
    private function validatePackIntoManyResponse(string $data): array
    {
        $decoded = JsonHelper::decode($data);

        $dummyResult = [
            'id' => -1,
            'w' => 0,
            'h' => 0,
            'd' => 0,
        ];
        $response = $decoded['response']
            ?? throw new Exception(is_string($decoded['error']) ? $decoded['error'] : 'Unknown error from API.');
        if (! is_array($response)) {
            throw new Exception('Unexpected API response format.');
        }
        $binsPacked = is_array($response['bins_packed'])
            ? $response['bins_packed']
            : throw new Exception('No binsPacked in API response.');
        if (
            count($binsPacked) === 0
            || count($binsPacked) > 1
        ) {
            return $dummyResult;
        }

        $singlePackedBin = is_array($binsPacked[0]) ? $binsPacked[0] : throw new Exception('No packed bin in API response.');
        $binData = is_array($singlePackedBin['bin_data'])
            ? $singlePackedBin['bin_data']
            : throw new Exception('No bin_data in API response.');

        return [
            'id' => (is_numeric($binData['id']))
                ? intval($binData['id'])
                : throw new Exception('No bin id in API response.'),
            'w' => (is_numeric($binData['w']))
                ? floatval($binData['w'])
                : throw new Exception('No bin width in API response.'),
            'h' => (is_numeric($binData['h']))
                ? floatval($binData['h'])
                : throw new Exception('No bin height in API response.'),
            'd' => (is_numeric($binData['d']))
                ? floatval($binData['d'])
                : throw new Exception('No bin depth(length) in API response.'),
        ];
    }

    private function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param array<string, mixed> $params
     */
    private function prepareQueryParams(array $params): string
    {
        return JsonHelper::encode(array_merge(
            [
                'username' => $this->userName,
                'api_key' => $this->apiKey,
            ],
            $params
        ));
    }
}
