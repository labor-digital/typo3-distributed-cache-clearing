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
 * Last modified: 2022.05.13 at 12:11
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\ExtConfigHandler;


use InvalidArgumentException;
use LaborDigital\T3ba\ExtConfig\Abstracts\AbstractExtConfigConfigurator;
use TYPO3\CMS\Core\Cache\Backend\ApcuBackend;
use TYPO3\CMS\Core\Cache\Backend\BackendInterface;
use TYPO3\CMS\Core\Cache\Backend\FileBackend;
use TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;

class DccConfigurator extends AbstractExtConfigConfigurator
{
    /**
     * The registered configuration for the message bus backend
     *
     * @var array|null
     */
    protected $messageBackendConfig;
    
    /**
     * The directory where the client id should be stored at
     *
     * @var string
     */
    protected $clientIdStoragePath = BETTER_API_TYPO3_VAR_PATH;
    
    /**
     * A list of cache backend classes that should be cleared when a cache clear message has been received
     *
     * @var array
     */
    protected $clearableCacheBackends
        = [
            FileBackend::class => true,
            ApcuBackend::class => true,
            SimpleFileBackend::class => true,
            TransientMemoryBackend::class => true,
        ];
    
    /**
     * If set to true the cache clear messages will be checked in every TYPO3 request
     * If disabled, you have to run the process either by using the T3BA route,
     * or the provided handleMessages.php somewhere in your application logic.
     *
     * @var bool
     */
    protected $checkInEveryRequest = false;
    
    /**
     * Sets the backend to be used to send and retrieve the messages with.
     *
     * @param   string      $backendClass  The name of the backend class to use in this container.
     *                                     The class must exist and implement the {@link \LaborDigital\T3dcc\Core\Message\Backend\MessageBackendInterface}
     * @param   array|null  $options       Additional options to be provided to the backend.
     *                                     Consult the documentation of the backend class to see which options are supported
     *
     * @return $this
     */
    public function setMessageBackend(string $backendClass, ?array $options = null): self
    {
        $this->messageBackendConfig = [$backendClass, $options ?? []];
        
        return $this;
    }
    
    /**
     * Returns either an array where item 0 is the name of the message backend in use and item 1 is the provided option list for it.
     * Alternatively it will return null if there is no active message backend provided
     *
     * @return array|null
     */
    public function getMessageBackendConfig(): ?array
    {
        return $this->messageBackendConfig;
    }
    
    /**
     * Removes any previously set message backend configuration; effectively disabling this extension.
     *
     * @return $this
     */
    public function removeMessageBackend(): self
    {
        $this->messageBackendConfig = null;
        
        return $this;
    }
    
    /**
     * Defines the path to the DIRECTORY, where the client id should be stored.
     * By default, BETTER_API_TYPO3_VAR_PATH is used as storage location.
     * The storage path MUST be located >inside< the container! If you use a directory in a shared, attached file-storage
     * this logic will not work, as all containers will use the same client id.
     *
     * @param   string  $storagePath  The absolute path to the storage location.
     *
     * @return $this
     */
    public function setClientIdStoragePath(string $storagePath): self
    {
        $this->clientIdStoragePath = $storagePath;
        
        return $this;
    }
    
    /**
     * Returns the configured directory where the client id should be stored at
     *
     * @return string
     */
    public function getClientIdStoragePath(): string
    {
        return $this->clientIdStoragePath;
    }
    
    /**
     * Defines a list of cache backend classes that should be flushed when the container receives a distributed cache clear message.
     * In a normal use-case you don't need to flush db or redis backends on every container. So only the common file-based backends will be flushed by default.
     *
     * @param   array  $backendClassNames  The list of class names that implement {@see BackendInterface}, which should be cleared
     *                                     when the container receives a clear cache message.
     *
     * @return $this
     */
    public function setClearableCacheBackends(array $backendClassNames): self
    {
        $this->clearableCacheBackends = [];
        
        foreach ($backendClassNames as $backendClassName) {
            $this->addClearableCacheBackend($backendClassName);
        }
        
        return $this;
    }
    
    /**
     * Registers the class of a cache backend to be flushed when the container receives a distributed cache clear message.
     *
     * @param   string  $className  The name of a class that implements {@see BackendInterface}, that should be cleared
     *                              when the container receives a clear cache message.
     *
     * @return $this
     */
    public function addClearableCacheBackend(string $className): self
    {
        if (! class_exists($className)) {
            throw new InvalidArgumentException('Invalid cache backend given! Class: "' . $className . '" does not exist');
        }
        
        if (! in_array(BackendInterface::class, class_implements($className), true)) {
            throw new InvalidArgumentException('Invalid cache backend given! Class: "' . $className . '" does not implement the required interface: "' .
                                               BackendInterface::class . '"');
        }
        
        $this->clearableCacheBackends[$className] = true;
        
        return $this;
    }
    
    /**
     * Returns the list of all cache backends that should be cleared when the container receives a cache clear event
     *
     * @return array
     */
    public function getClearableCacheBackends(): array
    {
        return array_keys($this->clearableCacheBackends);
    }
    
    /**
     * Returns true if the cache clear messages will be checked in every TYPO3 request
     *
     * @return bool
     */
    public function checkInEveryRequest(): bool
    {
        return $this->checkInEveryRequest;
    }
    
    /**
     * If set to true the cache clear messages will be checked in every TYPO3 request
     * If disabled, you have to run the process either by using the T3BA route,
     * or the provided handleMessages.php somewhere in your application logic.
     *
     * WARNING: Depending on your backend - this might become really slow!
     *
     * @param   bool  $checkInEveryRequest
     *
     * @return \LaborDigital\T3dcc\ExtConfigHandler\DccConfigurator
     */
    public function setCheckInEveryRequest(bool $checkInEveryRequest): self
    {
        $this->checkInEveryRequest = $checkInEveryRequest;
        
        return $this;
    }
}
