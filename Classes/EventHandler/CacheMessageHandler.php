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
 * Last modified: 2022.05.13 at 16:12
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\EventHandler;


use LaborDigital\T3dcc\Core\ClearCacheService;
use LaborDigital\Typo3BetterApi\Event\Events\ExtLocalConfLoadedEvent;
use Neunerlei\EventBus\Subscription\EventSubscriptionInterface;
use Neunerlei\EventBus\Subscription\LazyEventSubscriberInterface;

class CacheMessageHandler implements LazyEventSubscriberInterface
{
    /**
     * If set to true the cache messages will be checked on every request
     *
     * @internal
     * @var bool
     * @deprecated will be removed in v10
     */
    public static $checkOnEveryRequest = false;

    /**
     * @var \LaborDigital\T3dcc\Core\ClearCacheService
     */
    protected $cacheService;

    public function __construct(ClearCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @inheritDoc
     */
    public static function subscribeToEvents(EventSubscriptionInterface $subscription): void
    {
        $subscription->subscribe(ExtLocalConfLoadedEvent::class, 'onBoot');
    }

    public function onBoot(): void
    {
        if (! static::$checkOnEveryRequest) {
            return;
        }

        $this->cacheService->clearCacheIfRequired();
    }

}
