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
 * Last modified: 2022.05.24 at 12:47
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core\Message\Backend\Registry;


use LaborDigital\T3dcc\Core\Message\Backend\MessageBackendInterface;
use LaborDigital\T3dcc\Core\Message\Message;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\Registry;

class RegistryMessageBackend implements MessageBackendInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    protected const NAMESPACE = 't3dcc:messages';

    /**
     * @var \TYPO3\CMS\Core\Registry
     */
    protected $registry;

    protected $clientId;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @inheritDoc
     */
    public function setOptions(array $options): void
    {
    }

    /**
     * @inheritDoc
     */
    public function initialize(string $clientId, bool $firstTime): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @inheritDoc
     */
    public function getFlushCacheMessage(): ?Message
    {
        if (! isset($this->clientId)) {
            if ($this->logger) {
                $this->logger->error('Executed ' . __FUNCTION__ . ' before running initialize()');
            }

            return null;
        }

        $msg = $this->registry->get(static::NAMESPACE, 'message');
        if (! $msg) {
            return null;
        }

        if ($this->registry->get(static::NAMESPACE, $this->clientId) !== null) {
            return null;
        }

        $this->registry->set(static::NAMESPACE, $this->clientId, true);

        return Message::fromArray($msg);
    }

    /**
     * @inheritDoc
     */
    public function sendFlushCacheMessage(Message $message): void
    {
        if (! isset($this->clientId)) {
            if ($this->logger) {
                $this->logger->error('Executed ' . __FUNCTION__ . ' before running initialize()');
            }

            return;
        }

        $this->registry->removeAllByNamespace(static::NAMESPACE);
        $this->registry->set(static::NAMESPACE, 'message', $message->toArray());
        $this->registry->set(static::NAMESPACE, $this->clientId, true);
    }

}
