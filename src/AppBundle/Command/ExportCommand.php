<?php

namespace AppBundle\Command;

use AppBundle\Service\Export\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportCommand extends Command
{
    protected static $defaultName = 'app:export';

    private $serializer;

    public function __construct(Serializer $serializer)
    {
        parent::__construct();
        $this->serializer = $serializer;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Export data to file in any format')
            ->addArgument('entity', InputArgument::REQUIRED)
            ->addArgument('format', InputArgument::OPTIONAL, 'Format type to export', 'json');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entity = $input->getArgument('entity');
        $format = $input->getArgument('format');
        $this->serializer->serialize($entity, $format);

        $output->writeln(sprintf('Export encji %s do pliczku w formacie: %s ;)', $entity, $format));
    }
}
