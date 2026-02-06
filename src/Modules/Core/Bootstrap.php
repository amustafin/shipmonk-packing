<?php

declare(strict_types=1);

namespace App\Modules\Core;

use DI\Container;
use RuntimeException;

class Bootstrap
{
    public Container $diContainer {
        get {
            return $this->diContainer;
        }
    }

    private static ?self $instance = null;

    /**
     * @throws RuntimeException
     */
    private function __construct()
    {
        $this->initDIContainer();
    }

    /**
     * @throws RuntimeException
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @throws RuntimeException
     */
    private function initDIContainer(): void
    {
        $config = Config::fromYaml(__DIR__ . '/../../../config-local.yml');
        $this->diContainer = new Configurator($config)->createDiContainer();
    }
}
