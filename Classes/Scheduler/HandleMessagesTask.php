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
 * Last modified: 2022.05.24 at 11:00
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Scheduler;


use LaborDigital\T3ba\Core\Di\ContainerAwareTrait;
use LaborDigital\T3ba\ExtConfig\ExtConfigContext;
use LaborDigital\T3ba\ExtConfigHandler\Scheduler\Task\ConfigureTaskInterface;
use LaborDigital\T3ba\ExtConfigHandler\Scheduler\Task\TaskConfigurator;
use LaborDigital\T3dcc\Core\ClearCacheService;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

class HandleMessagesTask extends AbstractTask implements ConfigureTaskInterface
{
    use ContainerAwareTrait;
    
    /**
     * @inheritDoc
     */
    public static function configure(TaskConfigurator $taskConfigurator, ExtConfigContext $context): void
    {
        $taskConfigurator->setTitle('T3DCC: Handle messages')
                         ->setDescription(
                             'Clears the configured caches if a clear-cache message was received by another instance. This job must RUN ON ALL INSTANCES!');
    }
    
    /**
     * @inheritDoc
     */
    public function execute(): bool
    {
        $this->makeInstance(ClearCacheService::class)->clearCacheIfRequired();
        
        return true;
    }
    
}
