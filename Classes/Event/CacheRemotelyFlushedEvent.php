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
 * Last modified: 2022.05.24 at 19:40
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Event;


class CacheRemotelyFlushedEvent
{
    /**
     * @var array
     */
    protected $flushedCaches;
    
    /**
     * @var array|null
     */
    protected $groups;
    
    /**
     * @var array|null
     */
    protected $tags;
    
    public function __construct(array $flushedCaches, ?array $groups, ?array $tags)
    {
        $this->flushedCaches = $flushedCaches;
        $this->groups = $groups;
        $this->tags = $tags;
    }
    
    /**
     * Returns the list of cache keys that have been flushed by a remote
     *
     * @return array
     */
    public function getFlushedCaches(): array
    {
        return $this->flushedCaches;
    }
    
    /**
     * Returns either null or an array of group names that have been flushed
     *
     * @return array|null
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }
    
    /**
     * Returns either null or an array of tags that have been flushed
     *
     * @return array|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }
    
    
}