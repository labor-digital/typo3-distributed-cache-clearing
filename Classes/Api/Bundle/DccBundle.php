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
 * Last modified: 2022.05.25 at 11:41
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Api\Bundle;


use LaborDigital\T3ba\ExtConfig\SiteBased\SiteConfigContext;
use LaborDigital\T3dcc\Api\Route\DccRoute;
use LaborDigital\T3fa\ExtConfigHandler\Api\ApiBundleInterface;
use LaborDigital\T3fa\ExtConfigHandler\Api\ApiConfigurator;
use LaborDigital\T3fa\ExtConfigHandler\Api\Resource\ResourceCollector;

class DccBundle implements ApiBundleInterface
{
    public static function registerResources(ResourceCollector $collector, SiteConfigContext $context, array $options): void
    {
    }
    
    public static function configureSite(ApiConfigurator $configurator, SiteConfigContext $context, array $options): void
    {
        $configurator->routing()->routes('/dcc')->get(
            'clearIfRequired', [DccRoute::class, 'clearAction']
        );
    }
    
}