<?php

declare(strict_types=1);

namespace App\Modules\Core;

use App\Modules\Packaging\RemotePackager\API\PackagingApi;
use DI\Container;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\ORMSetup;

readonly class Configurator
{
    public function __construct(
        private Config $config,
    ) {
    }

    public function createDiContainer(): Container
    {
        $config = $this->config;
        return new Container([
            EntityManager::class => function () use ($config) {
                $ormConfig = ORMSetup::createAttributeMetadataConfiguration([$config->rootDir], true);
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
    }
}
