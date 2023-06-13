<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Config;

use Magento\Framework\ObjectManagerInterface;

/**
 * Provides list of commands to be available for uninstalled application
 */
class CommandList implements \Magento\Framework\Console\CommandListInterface
{
    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;
    
    private $commandClasses = [
        \Magento\Framework\Config\Command\Compile::class,
    ];

    /**
     * @param ObjectManagerInterface $objectManager Object Manager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritdoc
     */
    public function getCommands()
    {
        $commands = [];
        foreach ($this->commandClasses as $class) {
            $commands[] = $this->objectManager->get($class);
        }
        return $commands;
    }
}
