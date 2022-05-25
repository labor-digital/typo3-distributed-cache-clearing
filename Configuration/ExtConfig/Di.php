<?php
/*
 * Copyright 2022 LABOR.digital
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Last modified: 2022.05.24 at 18:57
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Configuration\ExtConfig;


use LaborDigital\T3ba\ExtConfig\ExtConfigContext;
use LaborDigital\T3ba\ExtConfigHandler\Di\ConfigureDiInterface;
use LaborDigital\T3dcc\Api\Route\DccRoute;
use LaborDigital\T3dcc\Command\HandleMessagesCommand;
use LaborDigital\T3dcc\Core\Cache\CacheFlusher;
use LaborDigital\T3dcc\Core\ClearCacheService;
use LaborDigital\T3dcc\Core\ClientId\ClientIdProvider;
use LaborDigital\T3dcc\Core\Message\Backend\Redis\RedisMessageBackend;
use LaborDigital\T3dcc\Core\Message\Backend\Registry\RegistryMessageBackend;
use LaborDigital\T3dcc\Core\Message\MessageBus;
use LaborDigital\T3dcc\ExtConfigHandler\Applier;
use LaborDigital\T3dcc\Util\MessageHandlerApplication;
use LaborDigital\T3fa\Core\Routing\Controller\AbstractRouteController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class Di implements ConfigureDiInterface
{
    /**
     * @inheritDoc
     */
    public static function configure(ContainerConfigurator $configurator, ContainerBuilder $containerBuilder, ExtConfigContext $context): void
    {
        $services = $configurator->services();
        
        $services->defaults()->autowire()->autoconfigure();
        
        $services->set(HandleMessagesCommand::class)
                 ->set(CacheFlusher::class)
                 ->set(ClientIdProvider::class)
                 ->set(MessageBus::class)
                 ->set(RedisMessageBackend::class)
                 ->set(RegistryMessageBackend::class)
                 ->set(ClearCacheService::class)
                 ->set(MessageHandlerApplication::class)->public()
                 ->set(Applier::class);
        
        if (class_exists(AbstractRouteController::class)) {
            $services->set(DccRoute::class);
        }
    }
    
    /**
     * @inheritDoc
     */
    public static function configureRuntime(Container $container, ExtConfigContext $context): void
    {
    }
}