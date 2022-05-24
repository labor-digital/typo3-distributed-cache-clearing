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
 * Last modified: 2022.05.13 at 15:34
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\ExtConfig;


use LaborDigital\T3dcc\Command\HandleMessagesCommand;
use LaborDigital\T3dcc\EventHandler\CacheClearHandler;
use LaborDigital\T3dcc\EventHandler\CacheMessageHandler;
use LaborDigital\T3dcc\ExtConfigHandler\DistCacheConfigOption;
use LaborDigital\T3dcc\Scheduler\HandleMessagesTask;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigContext;
use LaborDigital\Typo3BetterApi\ExtConfig\ExtConfigInterface;
use LaborDigital\Typo3BetterApi\ExtConfig\Extension\ExtConfigExtensionInterface;
use LaborDigital\Typo3BetterApi\ExtConfig\Extension\ExtConfigExtensionRegistry;
use LaborDigital\Typo3BetterApi\ExtConfig\OptionList\ExtConfigOptionList;

class DccExtConfig implements ExtConfigInterface, ExtConfigExtensionInterface
{

    /**
     * @inheritDoc
     */
    public function configure(ExtConfigOptionList $configurator, ExtConfigContext $context): void
    {
        $configurator->event()->registerLazySubscriber(CacheClearHandler::class)
                     ->registerLazySubscriber(CacheMessageHandler::class);

        $configurator->backend()->registerSchedulerTask(
            'T3DCC: Handle messages',
            HandleMessagesTask::class,
            'Clears the configured caches if a clear-cache message was received by another instance. This job must RUN ON ALL INSTANCES!'
        );

        $configurator->backend()->registerCommand(HandleMessagesCommand::class, ['commandName' => 't3dcc:handleMessages']);
    }

    /**
     * @inheritDoc
     */
    public static function extendExtConfig(ExtConfigExtensionRegistry $extender, ExtConfigContext $context): void
    {
        $extender->registerOptionListEntry(DistCacheConfigOption::class);
    }


}
