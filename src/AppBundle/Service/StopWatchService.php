<?php

namespace AppBundle\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;

class StopWatchService
{
    private $stopwatch;

    private $projectDir;

    public function __construct(Stopwatch $stopwatch, string $projectDir)
    {
        $this->stopwatch = $stopwatch;
        $this->projectDir = $projectDir;
    }

    public function testStopWatch(): string
    {
        $file = sprintf('%s/stop_watch.txt', $this->projectDir);
        if (file_exists($file)) {
            $fileSystem = new Filesystem();
            $fileSystem->remove($file);
        }

        $this->stopwatch->start('foo', 'bar');

        $result = '';
        for ($i = 1; $i <= 10000; $i++) {
            if ($i %10 === 0) {
                $result .= sprintf('%d,%s,', $i, PHP_EOL);
            }
            $result .= sprintf('%d,', $i);
        }

        file_put_contents($file, $result);

        $event = $this->stopwatch->stop('foo');
        dump($event);

        return $result;
    }
}