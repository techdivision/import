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

/**
 * A renderer for a simple debug report.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli
 * @link      http://www.techdivision.com
 */
class ReportFileRenderer extends AbstractDebugRenderer
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

        // initialize the array for the lines
        $lines = array();

        // log the Magento + the system's PHP configuration
        $lines[] = sprintf('Magento Edition: %s', $this->getConfiguration()->getMagentoEdition());
        $lines[] = sprintf('Magento Version: %s', $this->getConfiguration()->getMagentoVersion());
        $lines[] = sprintf('PHP Version: %s', phpversion());
        $lines[] = sprintf('App Version: %s', $this->getApplicationVersion());
        $lines[] = '-------------------- Loaded Extensions -----------------------';
        $lines[] = implode(', ', get_loaded_extensions());
        $lines[] = '------------------- Executed Operations ----------------------';
        $lines[] = implode(' > ', $this->getConfiguration()->getOperationNames());

        // finally write the debug report to a file in the source directory
        $this->write(implode(PHP_EOL, $lines), sprintf('%s/debug-report.txt', $sourceDir));
    }
}
