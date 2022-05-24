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
 * Last modified: 2022.05.24 at 19:01
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\ExtConfigHandler;


use LaborDigital\T3ba\Core\Di\ContainerAwareTrait;
use LaborDigital\T3ba\Event\Core\CacheClearedEvent;
use LaborDigital\T3ba\Event\Core\ExtLocalConfLoadedEvent;
use LaborDigital\T3ba\ExtConfig\Abstracts\AbstractExtConfigApplier;
use LaborDigital\T3dcc\Core\ClearCacheService;
use LaborDigital\T3dcc\Core\Message\Message;
use LaborDigital\T3dcc\Core\Message\MessageBus;
use Neunerlei\EventBus\Subscription\EventSubscriptionInterface;

class Applier extends AbstractExtConfigApplier
{
    use ContainerAwareTrait;
    
    /**
     * @inheritDoc
     */
    public static function subscribeToEvents(EventSubscriptionInterface $subscription): void
    {
        $subscription->subscribe(CacheClearedEvent::class, 'onCacheClear');
        $subscription->subscribe(ExtLocalConfLoadedEvent::class, 'onBoot');
    }
    
    /**
     * Dispatches the flush cache message to the other containers
     *
     * @param   \LaborDigital\T3ba\Event\Core\CacheClearedEvent  $event
     *
     * @return void
     */
    public function onCacheClear(CacheClearedEvent $event): void
    {
        $messageBus = $this->makeInstance(MessageBus::class);
        
        if (! $messageBus->hasConfig()) {
            return;
        }
        
        $messageBus->sendFlushCacheMessage(
            new Message([$event->getGroup()], $event->getTags())
        );
    }
    
    /**
     * Handles the cache clearing on every request if enabled in the options
     *
     * @return void
     */
    public function onBoot(): void
    {
        if (! $this->state->get('t3dcc.checkInEveryRequest')) {
            return;
        }
        
        $this->makeInstance(ClearCacheService::class)->clearCacheIfRequired();
    }
}