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
 * Last modified: 2022.05.24 at 10:25
 */

declare(strict_types=1);

// Exit early if php requirement is not satisfied.
use LaborDigital\T3dcc\Util\MessageHandlerApplication;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;

if (PHP_VERSION_ID < 70200) {
    die('This version of TYPO3 CMS requires PHP 7.2 or above');
}

(static function () {
    if (defined('T3DCC_AUTOLOAD_PATH')) {
        $autoloadPath = T3DCC_AUTOLOAD_PATH;
    } elseif (getenv('T3DCC_AUTOLOAD_PATH')) {
        $autoloadPath = getenv('T3DCC_AUTOLOAD_PATH');
    } else {
        $autoloadPath = dirname(getcwd()) . '/vendor/autoload.php';
    }

    if (! is_file($autoloadPath)) {
        die('There is no composer installation resolvable. In that case you must set the path in: "T3DCC_AUTOLOAD_PATH"');
    }

    if (defined('T3DCC_ENTRY_PATH')) {
        $entryPath = T3DCC_ENTRY_PATH;
    } elseif (getenv('T3DCC_ENTRY_PATH')) {
        $entryPath = getenv('T3DCC_ENTRY_PATH');
    } else {
        $docRoot   = $_SERVER['CONTEXT_DOCUMENT_ROOT'] ?? $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
        $entryPath = $docRoot . '/index.php';
    }

    if (defined('T3DCC_ENTRY_LEVEL')) {
        $entryLevel = T3DCC_ENTRY_LEVEL;
    } elseif (getenv('T3DCC_ENTRY_LEVEL')) {
        $entryLevel = getenv('T3DCC_ENTRY_LEVEL');
    } else {
        $entryLevel = 0;
    }

    $classLoader  = require $autoloadPath;
    $_SERVER['_'] = $entryPath;
    SystemEnvironmentBuilder::run($entryLevel, SystemEnvironmentBuilder::REQUESTTYPE_CLI);

    // @todo in v10, this should use the container instance
    Bootstrap::init($classLoader);
    (new MessageHandlerApplication)->run();

})();
