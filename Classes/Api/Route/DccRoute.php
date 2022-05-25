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
 * Last modified: 2022.05.16 at 10:38
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Api\Route;


use LaborDigital\T3dcc\Core\ClearCacheService;
use LaborDigital\T3fa\Core\Routing\Controller\AbstractRouteController;
use Psr\Http\Message\ResponseInterface;

class DccRoute extends AbstractRouteController
{
    /**
     * @var \LaborDigital\T3dcc\Core\ClearCacheService
     */
    protected $cacheService;
    
    public function __construct(ClearCacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }
    
    public function clearAction(): ResponseInterface
    {
        $this->cacheService->clearCacheIfRequired();
        
        return $this->getJsonOkResponse();
    }
}
