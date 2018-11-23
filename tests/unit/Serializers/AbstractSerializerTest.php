<?php

/**
 * TechDivision\Import\Serializers\ValueCsvSerializerTest
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
* @copyright 2016 TechDivision GmbH <info@techdivision.com>
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Serializers;

use TechDivision\Import\Configuration\CsvConfigurationInterface;

/**
 * Test class for the SQL statement implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractSerializerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The default CSV configuration values.
     *
     * @var array
     */
    protected $defaultConfiguration = array(
        'getDelimiter' => ',',
        'getEnclosure' => '"',
        'getEscape' => '\\'
    );

    /**
     * Create and return a mock CSV configuration instance.
     *
     * @param array $configuration The configuration to use (will override with the default one)
     *
     * @return \TechDivision\Import\Configuration\CsvConfigurationInterface The configuration instance
     */
    protected function getMockConfiguration(array $configuration = array())
    {

        // merge the default configuration with the passed on
        $configuration = array_merge($this->defaultConfiguration, $configuration);

        // create a mock configuration instance
        $mockConfiguration = $this->getMockBuilder(CsvConfigurationInterface::class)->getMock();

        // mock the methods
        foreach ($configuration as $methodName => $returnValue) {
            // mock the methods
            $mockConfiguration->expects($this->any())
                ->method($methodName)
                ->willReturn($returnValue);
        }

        // return the mock configuration
        return $mockConfiguration;
    }
}
