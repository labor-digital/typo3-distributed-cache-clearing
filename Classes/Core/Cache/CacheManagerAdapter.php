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
 * Last modified: 2022.05.13 at 15:05
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core\Cache;


use TYPO3\CMS\Core\Cache\CacheManager;

class CacheManagerAdapter extends CacheManager
{
    /**
     * Helper to extract all cache configuration array of the provided cache manager instance
     *
     * @param   \TYPO3\CMS\Core\Cache\CacheManager  $manager
     *
     * @return array
     */
    public static function extractConfiguration(CacheManager $manager): array
    {
        return $manager->cacheConfigurations;
    }
}
