<?php

use App\API\PackagingApi;
use App\Helpers\Config;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;
use GuzzleHttp\Client;

require __DIR__ . '/../vendor/autoload.php';

$config = Config::fromYaml(__DIR__ . '/../config-local.yml');

$container = new DI\Container([
    EntityManager::class => function () use ($config) {
        $ormConfig = ORMSetup::createAttributeMetadataConfiguration([__DIR__], true);
        $ormConfig->setNamingStrategy(new UnderscoreNamingStrategy());

        return new EntityManager(DriverManager::getConnection([
            'driver' => $config->dbConfig->driver,
            'host' => $config->dbConfig->host,
            'user' => $config->dbConfig->user,
            'password' => $config->dbConfig->password,
            'dbname' => $config->dbConfig->dbname,
        ]), $ormConfig);
    },

    // API dependencies
    Client::class => function () use ($config) {
        return new Client([
            'base_uri' => $config->binPackagingConfig->baseUrl,
        ]);
    },
    PackagingApi::class => function (Client $client) use ($config) {
        return new PackagingApi(
            userName: $config->binPackagingConfig->user,
            apiKey: $config->binPackagingConfig->apiKey,
            client: $client
        );
    },
]);

return $container;
