<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Config\Command;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to set application mode
 */
class Compile extends Command
{
    /**
     * Name of "target application mode" input argument
     */
    const MODE_ARGUMENT = 'mode';

    /**
     * Object manager factory
     *
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * Inject dependencies
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $description = 'Compile config.';

        $this->setName('config:compile')
            ->setDescription($description)
            ->setDefinition([
                new InputArgument(
                    'modified_file',
                    InputArgument::OPTIONAL,
                    'Configuration file to re-read'
                ),
            ]);
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $modifiedFile = $input->getArgument('modified_file');
            $compiler = $this->objectManager->get(\Magento\Framework\Config\Compiler::class);
            $type = 'events';
            $compiler->compile($type);
            $output->writeln(sprintf('Compiled config type "%s"', $type));
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln($e->getTraceAsString());
            }
            // we must have an exit code higher than zero to indicate something was wrong
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }
}
