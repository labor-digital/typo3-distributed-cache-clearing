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
 * Last modified: 2022.05.16 at 10:43
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core;


use LaborDigital\T3dcc\Core\Cache\CacheFlusher;
use LaborDigital\T3dcc\Core\Message\MessageBus;
use TYPO3\CMS\Core\SingletonInterface;

class ClearCacheService implements SingletonInterface
{
    /**
     * @var \LaborDigital\T3dcc\Core\Message\MessageBus
     */
    protected $messageBus;
    
    /**
     * @var \LaborDigital\T3dcc\Core\Cache\CacheFlusher
     */
    protected $cacheFlusher;
    
    public function __construct(
        MessageBus $messageBus,
        CacheFlusher $cacheFlusher
    )
    {
        $this->messageBus = $messageBus;
        $this->cacheFlusher = $cacheFlusher;
    }
    
    /**
     * Checks if there is a new clear cache message in the bus and starts the
     * flushing of required caches in the local container
     *
     * @return void
     */
    public function clearCacheIfRequired(): void
    {
        if (! $this->messageBus->hasConfig()) {
            return;
        }
        
        $message = $this->messageBus->getFlushCacheMessage();
        if ($message) {
            $this->cacheFlusher->flushCaches($message->getGroups(), $message->getTags());
        }
    }
}
