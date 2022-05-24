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
 * Last modified: 2022.05.24 at 19:38
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Event;


use LaborDigital\T3dcc\Core\Message\Message;

/**
 * Allows you to filter messages, incoming from other containers in the group
 */
class IncomingMessageFilterEvent
{
    /**
     * @var \LaborDigital\T3dcc\Core\Message\Message|null
     */
    protected $message;
    
    public function __construct(?Message $message)
    {
        $this->message = $message;
    }
    
    /**
     * Returns the message retrieved from another container
     *
     * @return \LaborDigital\T3dcc\Core\Message\Message
     */
    public function getMessage(): ?Message
    {
        return $this->message;
    }
    
    /**
     * Allows you to replace the message received from another container
     *
     * @param   \LaborDigital\T3dcc\Core\Message\Message|null  $message
     *
     * @return \LaborDigital\T3dcc\Event\IncomingMessageFilterEvent
     */
    public function setMessage(?Message $message): self
    {
        $this->message = $message;
        
        return $this;
    }
}