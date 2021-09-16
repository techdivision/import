<?php

/**
 * TechDivision\Import\Loaders\HeaderMappingLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * Loader that loads the header mappings from the configuration
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class HeaderMappingLoader implements LoaderInterface
{

    /**
     * The array with the available header mappings.
     *
     * @var array
     */
    private $headerMappings;

    /**
     * Initializes the loader with the configuration instance.
     *
     * @param array \TechDivision\Import\Configuration\ConfigurationInterface $configuration The array with the values
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->headerMappings = $configuration->getHeaderMappings();
    }

    /**
     * Load's and return's the values.
     *
     * @param string $entityTypeCode The entity type code to return the header mappings for
     *
     * @return array The array with the values
     */
    public function load(string $entityTypeCode = null) : array
    {
        return isset($this->headerMappings[$entityTypeCode]) ? $this->headerMappings[$entityTypeCode] : array();
    }
}
