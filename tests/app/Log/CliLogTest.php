<?php
namespace AdrianTilita\ResourceExposer\Log;

use Symfony\Component\Console\Output\OutputInterface;
use PHPUnit\Framework\TestCase;

class CliLogTest extends TestCase
{
    /**
     * Dummy test the log usage
     */
    public function testLog()
    {
        $outputMock = $this->getMockBuilder(OutputInterface::class)->getMock();
        $outputMock->expects($this->once())
            ->method('writeln')
            ->with($this->equalTo('<fg=yellow>[DEBUG]</> Foo-Bar'));

        $cliLog = new CliLog($outputMock);
        $cliLog->log('', 'Foo-Bar');
    }
}
