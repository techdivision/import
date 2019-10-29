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

use TechDivision\Import\ConfigurationInterface;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\FrontendInputTypes;
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
     * The default configuration values.
     *
     * @var array
     */
    protected $defaultConfiguration = array(
        'getEntityTypeCode'         => EntityTypeCodes::CATALOG_PRODUCT,
        'getMultipleFieldDelimiter' => ',',
        'getMultipleValueDelimiter' => '|'
    );

    /**
     * The default CSV configuration values.
     *
     * @var array
     */
    protected $defaultCsvConfiguration = array(
        'getDelimiter' => ',',
        'getEnclosure' => '"',
        'getEscape'    => '\\'
    );

    /**
     * Returns the array with virtual entity types for testing purposes.
     *
     * @param array $entityTypes An array with additional entity types to merge
     *
     * @return array The array with the entity types
     */
    protected function getEntityTypes(array $entityTypes = array())
    {
        return array_merge(
            array(
                EntityTypeCodes::CATALOG_PRODUCT => array(
                    MemberNames::ENTITY_TYPE_ID   => 4,
                    MemberNames::ENTITY_TYPE_CODE => EntityTypeCodes::CATALOG_PRODUCT
                )
            ),
            $entityTypes
        );
    }

    /**
     * Returns an array with virtual attributes for testing purposes.
     *
     * @param array $attributes An array with additional attributes to merge
     *
     * @return array The array with the attributes
     */
    protected function getAttributes(array $attributes = array())
    {
        return array_merge(
            array(
                'ac_01' => array(
                    MemberNames::ATTRIBUTE_CODE => 'ac_01',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'ac_02' => array(
                    MemberNames::ATTRIBUTE_CODE => 'ac_02',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'delivery_date_1' => array(
                    MemberNames::ATTRIBUTE_CODE => 'delivery_date_1',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'my_boolean_attribute' => array(
                    MemberNames::ATTRIBUTE_CODE => 'my_boolean_attribute',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::BOOLEAN
                ),
                'my_select_attribute' => array(
                    MemberNames::ATTRIBUTE_CODE => 'my_select_attribute',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'my_multiselect_attribute' => array(
                    MemberNames::ATTRIBUTE_CODE => 'my_multiselect_attribute',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::MULTISELECT
                ),
                'Application' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Application',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'BulletText2' => array(
                    MemberNames::ATTRIBUTE_CODE => 'BulletText2',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'ClothingSize' => array(
                    MemberNames::ATTRIBUTE_CODE => 'ClothingSize',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'Colours' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Colours',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'Description' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Description',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'FlagNew' => array(
                    MemberNames::ATTRIBUTE_CODE => 'FlagNew',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'FlagSample' => array(
                    MemberNames::ATTRIBUTE_CODE => 'FlagSample',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'Manufacturer' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Manufacturer',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'Material' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Material',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'MergeUomFactor' => array(
                    MemberNames::ATTRIBUTE_CODE => 'MergeUomFactor',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'Packaging' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Packaging',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'PublishTo' => array(
                    MemberNames::ATTRIBUTE_CODE => 'PublishTo',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'Type' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Type',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => FrontendInputTypes::SELECT
                ),
                'BulletText2' => array(
                    MemberNames::ATTRIBUTE_CODE => 'BulletText2',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'Category1Header' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Category1Header',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'Category3Header' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Category3Header',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'Legend' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Legend',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'MainlinePageNumber' => array(
                    MemberNames::ATTRIBUTE_CODE => 'MainlinePageNumber',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'Properties' => array(
                    MemberNames::ATTRIBUTE_CODE => 'Properties',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'PubCodeRankingValue' => array(
                    MemberNames::ATTRIBUTE_CODE => 'PubCodeRankingValue',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'SpaceCode' => array(
                    MemberNames::ATTRIBUTE_CODE => 'SpaceCode',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'StyleNo' => array(
                    MemberNames::ATTRIBUTE_CODE => 'StyleNo',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'StyleNoHeader' => array(
                    MemberNames::ATTRIBUTE_CODE => 'StyleNoHeader',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'SubHeader' => array(
                    MemberNames::ATTRIBUTE_CODE => 'SubHeader',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'TableHead1' => array(
                    MemberNames::ATTRIBUTE_CODE => 'TableHead1',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'YNumberMaterial' => array(
                    MemberNames::ATTRIBUTE_CODE => 'YNumberMaterial',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                ),
                'SP_STATUS' => array(
                    MemberNames::ATTRIBUTE_CODE => 'SP_STATUS',
                    MemberNames::ENTITY_TYPE_ID => 4,
                    MemberNames::FRONTEND_INPUT => 'text'
                )
            ),
            $attributes
        );
    }

    /**
     * Create and return a mock configuration instance.
     *
     * @param array $configuration The configuration to use (will override with the default one)
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    protected function getMockConfiguration(array $configuration = array())
    {

        // merge the default configuration with the passed on
        $configuration = array_merge($this->defaultConfiguration, $configuration);

        // create a mock configuration instance
        $mockConfiguration = $this->getMockBuilder(ConfigurationInterface::class)->getMock();

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

    /**
     * Create and return a mock CSV configuration instance.
     *
     * @param array $csvConfiguration The CSV configuration to use (will override with the default one)
     *
     * @return \TechDivision\Import\Configuration\CsvConfigurationInterface The configuration instance
     */
    protected function getMockCsvConfiguration(array $csvConfiguration = array())
    {

        // merge the default configuration with the passed on
        $csvConfiguration = array_merge($this->defaultCsvConfiguration, $csvConfiguration);

        // create a mock configuration instance
        $mockCsvConfiguration = $this->getMockBuilder(CsvConfigurationInterface::class)->getMock();

        // mock the methods
        foreach ($csvConfiguration as $methodName => $returnValue) {
            // mock the methods
            $mockCsvConfiguration->expects($this->any())
                ->method($methodName)
                ->willReturn($returnValue);
        }

        // return the mock configuration
        return $mockCsvConfiguration;
    }
}
