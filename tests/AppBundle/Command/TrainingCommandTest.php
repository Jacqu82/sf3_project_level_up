<?php


namespace Tests\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class TrainingCommandTest extends KernelTestCase
{
    public function testExecute()
    {
        $kernel = static::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:training_command');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'name' => 'Pati'
        ]);

        $output = $commandTester->getDisplay();
        $this->assertContains('Kocham Cie Pati', $output);
    }
}