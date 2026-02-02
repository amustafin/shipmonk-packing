<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;

/** @var DI\Container $container */
$container = require_once __DIR__ . '/src/bootstrap.php';
$entityManager = $container->get(EntityManager::class);

// Return EntityManagerProvider for Doctrine CLI tools
return new SingleManagerProvider($entityManager);

