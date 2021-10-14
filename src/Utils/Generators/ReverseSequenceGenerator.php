<?php

/**
 * TechDivision\Import\Utils\Generators\ReverseSequenceGenerator
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils\Generators;

use TechDivision\Import\Utils\CacheKeys;
use TechDivision\Import\Services\RegistryProcessorInterface;

/**
 * Generator implementation that generates reeverse sequences starting from -1.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ReverseSequenceGenerator implements GeneratorInterface
{

    /**
     * The registry processor instance used to generate the inversed entity IDs.
     *
     * @var \TechDivision\Import\Services\RegistryProcessorInterface
     */
    private $registryProcessor;

    /**
     * Initializes the generator with the registry processor.
     *
     * @param \TechDivision\Import\Services\RegistryProcessorInterface $registryProcessor The registry processor instance
     */
    public function __construct(RegistryProcessorInterface $registryProcessor)
    {
        $this->registryProcessor = $registryProcessor;
    }

    /**
     * Creates a new negative sequence for caching purposes.
     *
     * @param string $counterName The counter name that has to be lowered
     *
     * @return int The unique sequence
     * @see \TechDivision\Import\Utils\Generators\GeneratorInterface::generate()
     */
    public function generate(string $counterName = 'generic')
    {
        return $this->registryProcessor->lowerCounter(CacheKeys::SEQUENCES, $counterName);
    }
}
