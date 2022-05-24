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
 * Last modified: 2022.05.24 at 13:09
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Core\Message;


class Message
{
    /**
     * @var array|null
     */
    protected $groups;

    /**
     * @var array|null
     */
    protected $tags;

    public function __construct(?array $groups, ?array $tags)
    {
        $this->groups = $groups;
        $this->tags   = $tags;
    }

    /**
     * Returns the list of cache groups that should be cleared
     *
     * @return array|null
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }

    /**
     * Sets the list of cache groups to be cleared
     *
     * @param   array|null  $groups
     *
     * @return Message
     */
    public function setGroups(?array $groups): Message
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Returns the list of cache tags that should be cleared
     *
     * @return array|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * Sets the list of cache tags that should be cleared
     *
     * @param   array|null  $tags
     *
     * @return Message
     */
    public function setTags(?array $tags): Message
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Returns the stored data as array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'groups' => $this->getGroups(),
            'tags'   => $this->getTags(),
        ];
    }

    /**
     * Creates a new instance of a message based on the array version returned by toArray()
     *
     * @param   array  $data
     *
     * @return static
     */
    public static function fromArray(array $data): self
    {
        return new self($data['groups'] ?? null, $data['tags'] ?? null);
    }
}
