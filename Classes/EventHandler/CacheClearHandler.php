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
 * Last modified: 2022.05.13 at 11:57
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\EventHandler;


use LaborDigital\T3dcc\Core\Message\Message;
use LaborDigital\T3dcc\Core\Message\MessageBus;
use LaborDigital\Typo3BetterApi\Event\Events\CacheClearedEvent;
use Neunerlei\EventBus\Subscription\EventSubscriptionInterface;
use Neunerlei\EventBus\Subscription\LazyEventSubscriberInterface;

class CacheClearHandler implements LazyEventSubscriberInterface
{
    /**
     * @var \LaborDigital\T3dcc\Core\Message\MessageBus
     */
    protected $messageBus;

    public function __construct(MessageBus $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    /**
     * @inheritDoc
     */
    public static function subscribeToEvents(EventSubscriptionInterface $subscription): void
    {
        $subscription->subscribe(CacheClearedEvent::class, 'onCacheClear');
    }

    /**
     * Dispatches the flush cache message to the other containers
     *
     * @param   \LaborDigital\Typo3BetterApi\Event\Events\CacheClearedEvent  $event
     *
     * @return void
     */
    public function onCacheClear(CacheClearedEvent $event): void
    {
        if (! $this->messageBus->hasConfig()) {
            return;
        }

        $this->messageBus->sendFlushCacheMessage(
            new Message([$event->getGroup()], $event->getTags())
        );
    }
}
