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
 * Last modified: 2022.05.24 at 10:15
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Util;


use LaborDigital\T3dcc\Core\ClearCacheService;
use TYPO3\CMS\Core\Authentication\CommandLineUserAuthentication;
use TYPO3\CMS\Core\Console\CommandApplication;
use TYPO3\CMS\Core\Console\CommandRegistry;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class MessageHandlerApplication extends CommandApplication
{
    /**
     * @inheritDoc
     * @noinspection PhpMissingParentConstructorInspection
     * @noinspection MagicMethodsValidityInspection
     */
    public function __construct(Context $context, ?CommandRegistry $_ = null)
    {
        $this->context = $context;
    }
    
    /**
     * @inheritDoc
     */
    public function run(callable $execute = null)
    {
        $this->initializeContext();
        
        Bootstrap::loadExtTables();
        Bootstrap::initializeBackendUser(CommandLineUserAuthentication::class);
        $GLOBALS['LANG'] = LanguageService::createFromUserPreferences($GLOBALS['BE_USER']);
        
        $cacheService = GeneralUtility::makeInstance(ClearCacheService::class);
        $cacheService->clearCacheIfRequired();
        
        if ($execute !== null) {
            call_user_func($execute);
        }
    }
    
}
