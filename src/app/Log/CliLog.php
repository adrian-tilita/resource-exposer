<?php
namespace AdrianTilita\ResourceExposer\Log;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CliLog
 * @package AdrianTilita\ResourceExposer\Log
 */
class CliLog implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var OutputInterface
     */
    private $outputInterface;

    /**
     * CliLog constructor.
     * @param OutputInterface $output
     */
    public function __construct(OutputInterface $output)
    {
        $this->outputInterface = $output;
    }

    /**
     * {@inheritdoc}
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        $this->outputInterface->writeln(
            sprintf("<fg=yellow>[DEBUG]</> %s", $message)
        );
    }
}
