<?php

use App\API\PackagingApi;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

print_r(getenv());
$container = new DI\Container([
    EntityManager::class => function () {
        $config = ORMSetup::createAttributeMetadataConfiguration([__DIR__], true);
        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        return new EntityManager(DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => 'shipmonk-packing-mysql',
            'user' => 'root',
            'password' => 'secret',
            'dbname' => 'packing',
        ]), $config);
    },

    // API dependencies
    Client::class => function () {
        return new Client(['base_uri' => 'https://eu.api.3dbinpacking.com/packer/']);
    },
    PackagingApi::class => function (Client $client) {
        return new PackagingApi(
            userName: 'nibike8723@azeriom.com',
            apiKey: '46f3ad3608c723cf0fecaf6d58d84485',
            client: $client
        );
    },
]);

return $container;
