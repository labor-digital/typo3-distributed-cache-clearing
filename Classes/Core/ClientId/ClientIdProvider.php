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
 * Last modified: 2022.05.13 at 12:51
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core\ClientId;


use Neunerlei\Configuration\State\ConfigState;
use Neunerlei\Configuration\State\LocallyCachedStatePropertyTrait;
use Neunerlei\FileSystem\Fs;
use Neunerlei\Inflection\Inflector;
use Neunerlei\PathUtil\Path;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\StringUtility;

class ClientIdProvider implements SingletonInterface
{
    use LocallyCachedStatePropertyTrait;
    
    protected $newClientId = false;
    protected $clientId;
    protected $storagePath = BETTER_API_TYPO3_VAR_PATH;
    
    public function __construct(ConfigState $configState)
    {
        $this->registerCachedProperty('storagePath', 't3dcc.clientIdStoragePath', $configState);
    }
    
    /**
     * Returns true if the id provided by getClientId() was generated new for this request.
     * This allows the backends to generate subscriptions or databases if required
     *
     * @return bool
     */
    public function isNewClientId(): bool
    {
        return $this->newClientId;
    }
    
    /**
     * Returns a unique id for this client/container. The client id will be persisted as long as the container exists
     *
     * @return string
     */
    public function getClientId(): string
    {
        if (isset($this->clientId)) {
            return $this->clientId;
        }
        
        $filename = $this->getStorageFilename();
        if (Fs::isReadable($filename)) {
            return $this->clientId = trim(Fs::readFile($filename));
        }
        
        $this->newClientId = true;
        $this->clientId = $this->generateClientId();
        Fs::writeFile($filename, $this->clientId);
        
        return $this->clientId;
    }
    
    /**
     * Completely removes the (potentially) stored client id from both memory and the storage
     *
     * @return void
     */
    public function flush(): void
    {
        $this->clientId = null;
        $this->newClientId = false;
        Fs::remove($this->getStorageFilename());
    }
    
    /**
     * Generates the storage filename of the local client id
     *
     * @return string
     */
    protected function getStorageFilename(): string
    {
        return Path::unifyPath($this->storagePath, '/') . 'dcc-client-id.txt';
    }
    
    /**
     * Generates a new, unique id for this client/container
     *
     * @return string
     */
    protected function generateClientId(): string
    {
        $params = [
            StringUtility::getUniqueId(),
            getenv('T3DCC_CLIENT_ID'),
            $_SERVER['SERVER_SIGNATURE'] ?? null,
            $_SERVER['SERVER_NAME'] ?? null,
            $_SERVER['SERVER_ADDR'] ?? null,
            $_SERVER['PATH'] ?? null,
        ];
        
        return Inflector::toUuid(implode('|', $params));
    }
}
