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
 * Last modified: 2022.05.13 at 12:04
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core\Message\Backend;


use LaborDigital\T3dcc\Core\Message\Message;
use TYPO3\CMS\Core\SingletonInterface;

interface MessageBackendInterface extends SingletonInterface
{
    /**
     * Injects the options provided in the configuration
     *
     * @param   array  $options
     *
     * @return void
     */
    public function setOptions(array $options): void;

    /**
     * Called once when the instance of the backend is created, AFTER setOptions() has been executed
     *
     * @param   string  $clientId   The unique client id of this container (keeps the same in the lifetime of the container)
     * @param   bool    $firstTime  True if the provided client id is provided to the backend for the first time.
     *                              In subsequent calls this will be false.
     *
     * @return void
     */
    public function initialize(string $clientId, bool $firstTime): void;

    /**
     * Has to check if there are new messages, and if so, must return the instance of the submitted
     * message back to the system
     *
     * @return Message
     */
    public function getFlushCacheMessage(): ?Message;

    /**
     * Must send the provided message to the other containers to tell them to delete their local caches
     *
     * @param   \LaborDigital\T3dcc\Core\Message\Message  $message
     *
     * @return void
     */
    public function sendFlushCacheMessage(Message $message): void;
}
