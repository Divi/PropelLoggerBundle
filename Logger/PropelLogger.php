<?php

namespace Divi\PropelLoggerBundle\Logger;

use Psr\Log\LoggerInterface;
use Symfony\Bridge\Propel1\Logger\PropelLogger as BasePropelLogger;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @author Sylvain Lorinet <sylvain.lorinet@gmail.com>
 */
class PropelLogger extends BasePropelLogger
{
    /**
     * Namespaces converted into regex
     *
     * @var string
     */
    private $namespaces;

    /**
     * @param LoggerInterface $logger
     * @param Stopwatch       $stopwatch
     * @param array           $namespaces
     */
    public function __construct(LoggerInterface $logger = null, Stopwatch $stopwatch = null, $namespaces = array())
    {
        parent::__construct($logger, $stopwatch);

        // Convert namespaces to regex
        $this->namespaces = '';
        foreach ($namespaces as $namespace) {
            $this->namespaces .= $namespace . '|';
        }

        $this->namespaces = substr($this->namespaces, 0, -1);
    }

    /**
     * @var boolean
     */
    private $isPrepared = false;

    /**
     * A convenience function for logging a debug event.
     *
     * @param mixed $message the message to log.
     */
    public function debug($message)
    {
        $add = true;

        if (null !== $this->stopwatch) {
            $trace = debug_backtrace();
            $method = $trace[2]['args'][2];

            $watch = 'Propel Query '.(count($this->queries)+1);
            if ('PropelPDO::prepare' === $method) {
                $this->isPrepared = true;
                $this->stopwatch->start($watch, 'propel');

                $add = false;
            } elseif ($this->isPrepared) {
                $this->isPrepared = false;
                $this->stopwatch->stop($watch);
            }
        }

        if ($add) {
            if (null !== $this->logger) {
                $this->logger->debug($message);
            }

            // Stacktrace process
            if (!isset($trace)) {
                $trace = debug_backtrace();
            }

            $cleanedTrace = array();
            foreach ($trace as $j => $line) {
                // Check before condition
                if (!isset($line['class'])) {
                    continue;
                }
                // Keeping only file with "BNS" namespace
                else if (preg_match('#' . $this->namespaces . '#', $line['class'])) {
                    $i = count($cleanedTrace);
                    $cleanedTrace[$i]['file']     = isset($line['file']) ? $line['file'] : $trace[$j-1]['file'];
                    $cleanedTrace[$i]['function'] = $line['function'];
                    $cleanedTrace[$i]['line']     = isset($line['line']) ? $line['line'] : $trace[$j-1]['line'];
                    $cleanedTrace[$i]['class']    = $line['class'];
                }
            }

            $this->queries[] = $message . ' | ' . json_encode($cleanedTrace);
        }
    }
}