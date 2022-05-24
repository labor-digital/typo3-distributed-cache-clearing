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
 * Last modified: 2022.05.13 at 15:03
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core\Cache;


use Neunerlei\Configuration\State\ConfigState;
use Neunerlei\Configuration\State\LocallyCachedStatePropertyTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\SingletonInterface;

class CacheFlusher implements LoggerAwareInterface, SingletonInterface
{
    use LoggerAwareTrait;
    use LocallyCachedStatePropertyTrait;
    
    /**
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager;
    
    /**
     * The list of backend classes that are available to be flushed.
     * An empty array means flush all caches
     *
     * @var array
     */
    protected $flushableBackends = [];
    
    public function __construct(CacheManager $cacheManager, ConfigState $configState)
    {
        $this->cacheManager = $cacheManager;
        $this->registerCachedProperty('flushableBackends', 't3dcc.clearableCacheBackends', $configState);
    }
    
    /**
     * Clears all caches that have a "flushable backend" and are registered with one or more
     * of the provided groups. If a list of tags is given, only the provided tags will be flushed.
     * If either $tags or $groups are empty, they are considered as "all".
     *
     * @param   array|null  $groups
     * @param   array|null  $tags
     *
     * @return void
     */
    public function flushCaches(?array $groups, ?array $tags): void
    {
        $configurations = CacheManagerAdapter::extractConfiguration($this->cacheManager);
        
        $clearAllGroups = in_array('all', $groups, true);
        $clearAllBackends = empty($this->flushableBackends);
        
        foreach ($configurations as $cacheKey => $configuration) {
            if (! $clearAllGroups &&
                ! ((is_array($configuration['groups'] ?? null)) && array_intersect($groups, $configuration['groups']))) {
                if ($this->logger) {
                    $this->logger->notice('Don\'t flush cache "' . $cacheKey . '", because not in allowed groups: "' . implode('", "', $groups) . '"');
                }
                
                continue;
            }
            
            if (! $clearAllBackends &&
                ! in_array($configuration['backend'] ?? null, $this->flushableBackends, true)
            ) {
                if ($this->logger) {
                    $this->logger->notice('Don\'t flush cache "' . $cacheKey . '", because the backend is not allowed to be fushed');
                }
                
                continue;
            }
            
            if ($this->logger) {
                $this->logger->info('Clearing cache: "' . $cacheKey . '" locally...');
            }
            
            if ($tags) {
                $this->cacheManager->getCache($cacheKey)->flushByTags($tags);
            } else {
                $this->cacheManager->getCache($cacheKey)->flush();
            }
        }
    }
}
