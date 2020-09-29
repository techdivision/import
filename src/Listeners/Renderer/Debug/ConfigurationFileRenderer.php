<?php

/**
 * TechDivision\Import\Listeners\Renderer\Debug\ReportFileRenderer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Listeners\Renderer\Debug;

use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Configuration\ConfigurationInterface;

/**
 * A simple renderer for the configuration instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli
 * @link      http://www.techdivision.com
 */
class ConfigurationFileRenderer extends AbstractDebugRenderer
{

    /**
     * Renders the data to some output, e. g. the console or a logger.
     *
     * @param string $serial The serial of the import to render the dump artefacts for
     *
     * @return void
     */
    public function render(string $serial = null)
    {

        // load the actual status
        $status = $this->getRegistryProcessor()->getAttribute(RegistryKeys::STATUS);

        // clear the filecache
        clearstatcache();

        // query whether or not the configured source directory is available
        if (!is_dir($sourceDir = $status[RegistryKeys::SOURCE_DIRECTORY])) {
            throw new \Exception(sprintf('Configured source directory %s is not available!', $sourceDir));
        }

        // finally write the serialized configuration file to the source directory
        $this->write($this->dump($this->getConfiguration()), sprintf('%s/debug-configuration.json', $sourceDir));
    }

    /**
     * Returns a string representation of the actual configuration instance.
     *
     * @param \TechDivision\Import\Configuration\ConfigurationInterface $configuration The configuration instance to dump
     *
     * @return string The string representation of the actual configuration instance
     */
    protected function dump(ConfigurationInterface $configuration) : string
    {
        return json_encode($configuration, JSON_OBJECT_AS_ARRAY);
    }
}
