<?php

declare(strict_types=1);

namespace App\Modules\Core;

use RuntimeException;
use Symfony\Component\Yaml\Yaml;

final readonly class Config
{
    private function __construct(
        public DatabaseConfig $dbConfig,
        public BinPackagingConfig $binPackagingConfig,
    ) {
    }

    /**
     * @throws RuntimeException
     */
    public static function fromYaml(string $path): self
    {
        $data = Yaml::parseFile($path);
        if (! is_array($data)) {
            throw new RuntimeException(sprintf('Invalid config file: %s', $path));
        }

        if (! isset($data['parameters']) || ! is_array($data['parameters'])) {
            throw new RuntimeException(sprintf('Missing or invalid "parameters" section in config file: %s', $path));
        }

        $parameters = $data['parameters'];

        // Validate database config
        if (! isset($parameters['database']) || ! is_array($parameters['database'])) {
            throw new RuntimeException(sprintf('Missing or invalid "database" configuration in config file: %s', $path));
        }

        $database = $parameters['database'];

        if (
            ! isset($database['driver'])
            || ! is_string($database['driver'])
            || ! in_array(
                $database['driver'],
                [
                    'ibm_db2',
                    'mysqli',
                    'oci8',
                    'pdo_mysql',
                    'pdo_oci',
                    'pdo_pgsql',
                    'pdo_sqlite',
                    'pdo_sqlsrv',
                    'pgsql',
                    'sqlite3',
                    'sqlsrv',
                ],
                true
            )
        ) {
            throw new RuntimeException('Missing or invalid "database.driver" in config file');
        }

        if (! isset($database['host']) || ! is_string($database['host'])) {
            throw new RuntimeException('Missing or invalid "database.host" in config file');
        }

        if (! isset($database['user']) || ! is_string($database['user'])) {
            throw new RuntimeException('Missing or invalid "database.user" in config file');
        }

        if (! isset($database['password']) || ! is_string($database['password'])) {
            throw new RuntimeException('Missing or invalid "database.password" in config file');
        }

        if (! isset($database['dbname']) || ! is_string($database['dbname'])) {
            throw new RuntimeException('Missing or invalid "database.dbname" in config file');
        }

        // Validate binPackingAPI config
        if (! isset($parameters['binPackingAPI']) || ! is_array($parameters['binPackingAPI'])) {
            throw new RuntimeException(sprintf('Missing or invalid "binPackingAPI" configuration in config file: %s', $path));
        }

        $binPackingAPI = $parameters['binPackingAPI'];

        if (! isset($binPackingAPI['baseUrl']) || ! is_string($binPackingAPI['baseUrl'])) {
            throw new RuntimeException('Missing or invalid "binPackingAPI.baseUrl" in config file');
        }

        if (! isset($binPackingAPI['user']) || ! is_string($binPackingAPI['user'])) {
            throw new RuntimeException('Missing or invalid "binPackingAPI.user" in config file');
        }

        if (! isset($binPackingAPI['apiKey']) || ! is_string($binPackingAPI['apiKey'])) {
            throw new RuntimeException('Missing or invalid "binPackingAPI.apiKey" in config file');
        }

        return new self(
            dbConfig: new DatabaseConfig(
                driver: $database['driver'],
                host: $database['host'],
                user: $database['user'],
                password: $database['password'],
                dbname: $database['dbname'],
            ),
            binPackagingConfig: new BinPackagingConfig(
                baseUrl: $binPackingAPI['baseUrl'],
                user: $binPackingAPI['user'],
                apiKey: $binPackingAPI['apiKey'],
            ),
        );
    }
}
