<?php

use App\Modules\Core\Config;
use App\Modules\Packaging\RemotePackager\API\PackagingApi;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;

require __DIR__ . '/../vendor/autoload.php';

$config = Config::fromYaml(__DIR__ . '/../config-local.yml');

$container = new DI\Container([
    EntityManager::class => function () use ($config) {
        $ormConfig = ORMSetup::createAttributeMetadataConfiguration([__DIR__], true);
        $ormConfig->setNamingStrategy(new UnderscoreNamingStrategy());

        return new EntityManager(DriverManager::getConnection([
            'driver' => $config->dbConfig->getDriver(),
            'host' => $config->dbConfig->host,
            'user' => $config->dbConfig->user,
            'password' => $config->dbConfig->password,
            'dbname' => $config->dbConfig->dbname,
        ]), $ormConfig);
    },

    PackagingApi::class => function () use ($config) {
        return new PackagingApi(config: $config->binPackagingConfig);
    },
]);

return $container;
