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
 * Last modified: 2022.05.24 at 11:02
 */

declare(strict_types=1);


namespace LaborDigital\T3dcc\Command;


use LaborDigital\T3ba\Core\Di\ContainerAwareTrait;
use LaborDigital\T3ba\ExtConfigHandler\Command\ConfigureCliCommandInterface;
use LaborDigital\T3dcc\Core\ClearCacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HandleMessagesCommand extends Command implements ConfigureCliCommandInterface
{
    use ContainerAwareTrait;
    
    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->setName('t3dcc:handleMessages');
        $this->setDescription('Clears the configured caches if a clear-cache message was received by another instance. This job must RUN ON ALL INSTANCES!');
    }
    
    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Checking messages...');
        
        $this->makeInstance(ClearCacheService::class)->clearCacheIfRequired();
        
        return 0;
    }
    
}
