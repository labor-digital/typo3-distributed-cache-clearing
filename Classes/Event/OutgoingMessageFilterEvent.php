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
 * Last modified: 2022.05.24 at 19:35
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Event;


use LaborDigital\T3dcc\Core\Message\Message;

/**
 * Allows you to modify a message before it is sent to other containers in the group
 */
class OutgoingMessageFilterEvent
{
    /**
     * @var \LaborDigital\T3dcc\Core\Message\Message
     */
    protected $message;
    
    public function __construct(Message $message)
    {
        $this->message = $message;
    }
    
    /**
     * Returns the message to be emitted to the other containers
     *
     * @return \LaborDigital\T3dcc\Core\Message\Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
    
    /**
     * Allows you to replace the message to be emitted to the other containers
     *
     * @param   \LaborDigital\T3dcc\Core\Message\Message  $message
     *
     * @return OutgoingMessageFilterEvent
     */
    public function setMessage(Message $message): OutgoingMessageFilterEvent
    {
        $this->message = $message;
        
        return $this;
    }
}