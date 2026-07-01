<?php

declare(strict_types=1);

namespace Tests\config;

use App\Modules\Core\Config;
use App\Modules\Core\Configurator;
use DI\Container;

class TestBootstrap
{
    private static ?self $instance = null;

    public Container $diContainer {
        get {
            return $this->diContainer;
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->initDIContainer();
    }

    private function initDIContainer(): void
    {
        $config = Config::fromYaml(__DIR__ . '/config-local.yml');
        $this->diContainer = new Configurator($config)->createDiContainer();
    }
}
