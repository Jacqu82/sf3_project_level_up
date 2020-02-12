<?php

namespace AppBundle\Command;

use AppBundle\DataFixtures\ORM\LoadFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class TrainingCommand extends Command
{
    protected static $defaultName = 'app:training_command';

    /**
     * @var LoadFixtures
     */
    private $loadFixtures;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(LoadFixtures $loadFixtures, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->loadFixtures = $loadFixtures;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Hello PhpStorm')
            ->addArgument('name', InputArgument::REQUIRED)
//            ->addArgument('number', InputArgument::REQUIRED, 'How many iterations')
//            ->addOption('modulo', 'm', InputOption::VALUE_OPTIONAL, 'is even?', false)
//            ->addArgument('multiple', InputArgument::REQUIRED, 'Set multiple of iteration')
//            ->addArgument('array', InputArgument::IS_ARRAY, 'Some training array')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $name = 'Kocham Cie skarbie';
//        $arr = str_split($name);
//        $iterations = $input->getArgument('number');
//        $modulo = filter_var($input->getOption('modulo'), FILTER_VALIDATE_BOOLEAN);
//        $multiple = $input->getArgument('multiple');
//        $array = $input->getArgument('array');


//        for ($i = 1; $i <= $iterations; $i++) {
//            if (true === $modulo && $i % 2 === 0) {
//                $output->writeln($i);
//            } elseif (false === $modulo && $i % 2 !== 0) {
//                $output->writeln($i);
//            }
//        }

//        $text = '';
//        if (count($array) > 0) {
//            $text .= ' '.implode(', ', $array);
//            $output->writeln($text);
//        }
//        for ($i = 1; $i <= $iterations; $i++) {
//            $output->writeln($i * ($multiple ?: 1) . PHP_EOL);
//        }
//        foreach ($arr as $letter) {
////            $output->writeln($letter . PHP_EOL);
//            $output->writeln('<fg=black;bg=cyan>'.$letter.'</>'. PHP_EOL);
//            sleep(1);
//        }

//        /** @var User[] $users */
//        $users = $this->entityManager->getRepository(User::class)->findAll();
//        foreach ($users as $user) {
//            $output->writeln($user->getUsername() . PHP_EOL);
//        }


//        $this->loadFixtures->load($this->entityManager);

        $name = $input->getArgument('name');
        $output->writeln(sprintf('Kocham Cie %s', $name));
    }
}
