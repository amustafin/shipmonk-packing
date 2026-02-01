<?php

declare(strict_types=1);

namespace App\API;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use SensitiveParameter;

final readonly class PackagingApi
{
    private function __construct(
        private string $userName,
        #[SensitiveParameter]
        private string $apiKey,
        private Client $client,
    ) {
    }

    public static function create(): self
    {
        $userName = 'lowefir406@azeriom.com';
        $apiToken = 'beaccdb88b96ece1e135cb503a275bf4';
        $baseUrl = "https://eu.api.3dbinpacking.com/packer/";

        $client = new Client(['base_uri' => $baseUrl]);
        return new self($userName, $apiToken, $client);
    }

    /**
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
        $decoded = json_decode($data, true);

        $dummyResult = ['id' => -1, 'w' => 0, 'h' => 0, 'd' => 0];
        $response = $decoded['response']
            ?? throw new Exception($response['error'] ?? 'Unknown error from API.');
        $binsPacked = $response['bins_packed']
            ?? throw new Exception('No binsPacked in API response.');
        if (
            count($binsPacked) === 0
            || count($binsPacked) > 1
        ) {
            return $dummyResult;
        }

        $binData = $binsPacked[0]['bin_data']
            ?? throw new Exception('No bin_data in API response.');

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

    private function prepareQueryParams(array $params): string
    {
        return json_encode(array_merge(
            [
                'username' => $this->userName,
                'api_key' => $this->apiKey,
            ],
            $params
        ));
    }
}
