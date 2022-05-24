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
 * Last modified: 2022.05.13 at 12:02
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core\Message;


use LaborDigital\T3ba\Core\Di\ContainerAwareTrait;
use LaborDigital\T3dcc\Core\ClientId\ClientIdProvider;
use LaborDigital\T3dcc\Core\Message\Backend\MessageBackendInterface;
use Neunerlei\Configuration\State\ConfigState;
use Neunerlei\Configuration\State\LocallyCachedStatePropertyTrait;
use RuntimeException;
use TYPO3\CMS\Core\SingletonInterface;

class MessageBus implements SingletonInterface
{
    use ContainerAwareTrait;
    use LocallyCachedStatePropertyTrait;
    
    /**
     * @var \LaborDigital\T3dcc\Core\ClientId\ClientIdProvider
     */
    protected $clientIdProvider;
    
    /**
     * The configuration provided by the config option
     *
     * @var array|null
     */
    protected $config;
    
    /**
     * The instance that has been resolved in createConcreteBackend()
     * NOTE: Don't use this property directly!
     *
     * @var MessageBackendInterface
     */
    protected $concreteBackend;
    
    public function __construct(
        ClientIdProvider $clientIdProvider,
        ConfigState $configState
    )
    {
        $this->clientIdProvider = $clientIdProvider;
        $this->registerCachedProperty('config', 't3dcc.messageBackendConfig', $configState);
    }
    
    /**
     * Returns true if there is some configuration provided, meaning this class needs to perform some actions.
     *
     * @return bool
     */
    public function hasConfig(): bool
    {
        return isset($this->config);
    }
    
    /**
     * Returns either the last flush cache message or null if there is no new one
     *
     * @return \LaborDigital\T3dcc\Core\Message\Message|null
     */
    public function getFlushCacheMessage(): ?Message
    {
        return $this->getConcreteBackend()->getFlushCacheMessage();
    }
    
    /**
     * Sends a flush cache message to the other containers in the group
     *
     * @param   \LaborDigital\T3dcc\Core\Message\Message  $message
     *
     * @return void
     */
    public function sendFlushCacheMessage(Message $message): void
    {
        $this->getConcreteBackend()->sendFlushCacheMessage($message);
    }
    
    /**
     * Internal factory to either create a new instance of the concrete backend, or re-uses the previously created instance
     *
     * @return \LaborDigital\T3dcc\Core\Message\Backend\MessageBackendInterface
     */
    protected function getConcreteBackend(): MessageBackendInterface
    {
        if (isset($this->concreteBackend)) {
            return $this->concreteBackend;
        }
        
        if (! is_array($this->config)) {
            throw new RuntimeException('Failed to create a message backend, because the config was not yet injected through setConfig()');
        }
        
        [$classname, $options] = $this->config;
        
        if (! class_exists($classname)) {
            throw new RuntimeException('There is no class "' . $classname . '" to create a message backend with');
        }
        
        $backend = $this->makeInstance($classname);
        
        if (! $backend instanceof MessageBackendInterface) {
            throw new RuntimeException(
                'The provided backend class: "' . $classname . '" is no instance of the backend interface: "' . MessageBackendInterface::class . '"');
        }
        
        $backend->setOptions(is_array($options) ? $options : []);
        $backend->initialize($this->clientIdProvider->getClientId(), $this->clientIdProvider->isNewClientId());
        
        return $this->concreteBackend = $backend;
    }
}
