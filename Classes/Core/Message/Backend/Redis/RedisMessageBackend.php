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
 * Last modified: 2022.05.24 at 15:04
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core\Message\Backend\Redis;


use LaborDigital\T3dcc\Core\Message\Backend\MessageBackendInterface;
use LaborDigital\T3dcc\Core\Message\Message;
use Neunerlei\Options\Options;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class RedisMessageBackend implements MessageBackendInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;
    
    protected const NAMESPACE = 't3dcc:messages.';
    protected const MESSAGE_KEY = self::NAMESPACE . 'message';
    
    protected $options;
    
    /**
     * @var \Redis
     */
    protected $backend;
    
    protected $clientId;
    
    public function __destruct()
    {
        if ($this->backend) {
            $this->backend->close();
        }
    }
    
    /**
     * @inheritDoc
     */
    public function setOptions(array $options): void
    {
        $this->options = Options::make($options, [
            'hostname' => [
                'type' => 'string',
            ],
            'password' => [
                'type' => ['string', 'null'],
                'default' => null,
            ],
            'database' => [
                'type' => 'int',
                'default' => 1,
            ],
            'port' => [
                'type' => 'int',
                'default' => 6379,
            ],
            'ttl' => [
                'type' => 'int',
                'default' => 60 * 15,
            ],
        ]);
    }
    
    /**
     * @inheritDoc
     */
    public function initialize(string $clientId, bool $firstTime): void
    {
        $this->clientId = $clientId;
        $this->backend = $this->connect();
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
        
        $msg = $this->backend->get(static::MESSAGE_KEY);
        if (! $msg) {
            return null;
        }
        
        if ($this->backend->get(static::NAMESPACE . $this->clientId) !== false) {
            return null;
        }
        
        $this->backend->set(static::NAMESPACE . $this->clientId, true);
        
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
        
        foreach ($this->backend->keys(static::NAMESPACE . '*') as $key) {
            $this->backend->del($key);
        }
        
        $this->backend->set(static::MESSAGE_KEY, $message->toArray());
        $this->backend->set(static::NAMESPACE . $this->clientId, true);
    }
    
    /**
     * Connects the
     *
     * @return \Redis
     */
    protected function connect(): \Redis
    {
        if (! class_exists(\Redis::class)) {
            throw new \RuntimeException('Failed to connect to redis backend, because the php extension is not available');
        }
        
        $backend = new \Redis();
        
        $backend->connect($this->options['hostname'], $this->options['port']);
        
        if (! empty($this->options['password'])) {
            $backend->auth($this->options['password']);
        }
        
        $backend->select($this->options['database']);
        
        $backend->setOption(\Redis::OPT_SERIALIZER, \Redis::SERIALIZER_JSON);
        
        return $backend;
    }
}
