<?php

declare(strict_types=1);

namespace App\Modules\Core;

use SensitiveParameter;

final class DatabaseConfig
{
    /**
     * @param 'ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv' $driver
     */
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
}
