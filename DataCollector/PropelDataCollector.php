<?php

namespace Divi\PropelLoggerBundle\DataCollector;

use Symfony\Bridge\Propel1\DataCollector\PropelDataCollector as BasePropelDataCollector;
use Symfony\Bridge\Propel1\Logger\PropelLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The PropelDataCollector collector class collects information.
 *
 * @author William Durand <william.durand1@gmail.com>
 * @author Sylvain Lorinet <sylvain.lorinet@gmail.com>
 */
class PropelDataCollector extends BasePropelDataCollector
{
    /**
     * Propel logger
     * @var \Symfony\Bridge\Propel1\Logger\PropelLogger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param PropelLogger         $logger              A Propel logger.
     * @param \PropelConfiguration $propelConfiguration The Propel configuration object.
     */
    public function __construct(PropelLogger $logger, \PropelConfiguration $propelConfiguration)
    {
        $this->logger              = $logger;
        $this->propelConfiguration = $propelConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $this->data = array(
            'queries'        => $this->buildQueries(),
            'querycount'     => $this->countQueries(),
            'duplicatecount' => $this->countDuplicateQueries()
        );
    }

    /**
     * Creates an array of Build objects.
     *
     * @return array  An array of Build objects
     */
    private function buildQueries()
    {
        $queries = array();

        $outerGlue = $this->propelConfiguration->getParameter('debugpdo.logging.outerglue', ' | ');
        $innerGlue = $this->propelConfiguration->getParameter('debugpdo.logging.innerglue', ': ');

        foreach ($this->logger->getQueries() as $q) {
            $parts     = explode($outerGlue, $q, 5);

            $times     = explode($innerGlue, $parts[0]);
            $con       = explode($innerGlue, $parts[2]);
            $memories  = explode($innerGlue, $parts[1]);

            $trace     = json_decode(trim($parts[4]));
            $sql       = trim($parts[3]);
            $con       = trim($con[1]);
            $time      = trim($times[1]);
            $memory    = trim($memories[1]);
            $duplicate = false;

            // Search for duplicate query
            foreach ($queries as $query) {
                if ($sql == $query['sql']) {
                    $duplicate = true;
                }
            }

            $queries[] = array(
                'connection' => $con,
                'sql'        => $sql,
                'time'       => $time,
                'memory'     => $memory,
                'stacktrace' => $trace,
                'duplicate'  => $duplicate
            );
        }

        return $queries;
    }

    /**
     * Count queries.
     *
     * @return int  The number of queries.
     */
    private function countQueries()
    {
        return count($this->logger->getQueries());
    }

    /**
     * Count the duplicate queries
     *
     * @return int The number of duplicate queries
     */
    private function countDuplicateQueries()
    {
        $queries = $this->buildQueries();
        $count = 0;

        foreach ($queries as $query) {
            if ($query['duplicate']) {
                ++$count;
            }
        }

        return $count;
    }

    /**
     * Returns the duplicate queries count.
     *
     * @return int The duplicate queries count
     */
    public function getDuplicateCount()
    {
        return $this->data['duplicatecount'];
    }
}