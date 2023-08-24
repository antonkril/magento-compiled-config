<?php

namespace Akril\Compiler\Command;

use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        $description = 'Compile assets for a file.';

        $this->setName('compile:file')
            ->setDescription($description)
            ->setDefinition([
                new InputArgument(
                    'modified_file',
                    InputArgument::REQUIRED,
                    'File to re-read'
                ),
            ]);
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compiler = $this->objectManager->get(\Akril\Compiler\IncrementalCompiler::class);
        $messages = $compiler->compile($input->getArgument('modified_file'));
        $output->writeln($messages);
        return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
    }
}
