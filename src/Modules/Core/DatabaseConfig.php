<?php

declare(strict_types=1);

namespace App\Modules\Core;

use pq\Exception\RuntimeException;
use SensitiveParameter;

final class DatabaseConfig
{
    public function __construct(
        public string $driver,
        #[SensitiveParameter]
        public string $host,
        #[SensitiveParameter]
        public string $user,
        #[SensitiveParameter]
        public string $password,
        #[SensitiveParameter]
        public string $dbname,
    ) {
    }

    /**
     * @return 'ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv'
     * @throws RuntimeException
     */
    public function getDriver(): string
    {
        if (in_array(
            $this->driver,
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
            ]
        )) {
            return $this->driver;
        }
        throw new RuntimeException();
    }
}
