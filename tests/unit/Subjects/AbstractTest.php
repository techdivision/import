<?php

/**
 * TechDivision\Import\Subjects\AbstractTest
 *
* PHP version 7
*
* @author    Tim Wagner <t.wagner@techdivision.com>
* @copyright 2016 TechDivision GmbH <info@techdivision.com>
* @license   https://opensource.org/licenses/MIT
* @link      https://github.com/techdivision/import
* @link      http://www.techdivision.com
*/

namespace TechDivision\Import\Subjects;

use PHPUnit\Framework\TestCase;
use Doctrine\Common\Collections\ArrayCollection;
use TechDivision\Import\Loaders\LoaderInterface;
use TechDivision\Import\Utils\LoggerKeys;
use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\EditionNames;
use TechDivision\Import\Utils\RegistryKeys;
use TechDivision\Import\Utils\EntityTypeCodes;
use TechDivision\Import\Utils\Generators\CoreConfigDataUidGenerator;
use TechDivision\Import\Configuration\Subject\DateConverterConfigurationInterface;
use TechDivision\Import\Configuration\ExecutionContextInterface;
use League\Event\EmitterInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Configuration\PluginConfigurationInterface;
use TechDivision\Import\Configuration\ConfigurationInterface;
use TechDivision\Import\Services\RegistryProcessorInterface;
use Psr\Log\LoggerInterface;

/**
 * Abstract subject test class.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractTest extends TestCase
{

    /**
     * Mock the global data.
     *
     * @return array The array with the global data
     */
    protected function getMockGlobalData(array $globalData = array())
    {
        return array_merge_recursive(
            $globalData,
            array(
                RegistryKeys::GLOBAL_DATA => array(
                    RegistryKeys::LINK_TYPES => array(),
                    RegistryKeys::CATEGORIES => array(),
                    RegistryKeys::TAX_CLASSES => array(),
                    RegistryKeys::EAV_ATTRIBUTES => array(),
                    RegistryKeys::ATTRIBUTE_SETS => array(),
                    RegistryKeys::STORE_WEBSITES => array(
                        'admin' => array(
                            MemberNames::WEBSITE_ID => 0,
                            MemberNames::CODE => 'admin',
                            MemberNames::NAME => 'Admin'
                        ),
                        'base' => array(
                            MemberNames::WEBSITE_ID => 1,
                            MemberNames::CODE => 'base',
                            MemberNames::NAME => 'Main Website'
                        )
                    ),
                    RegistryKeys::DEFAULT_STORE => array(
                        MemberNames::STORE_ID => 1,
                        MemberNames::CODE => 'default',
                        MemberNames::WEBSITE_ID => 1
                    ),
                    RegistryKeys::ROOT_CATEGORIES => array(
                        'default' => array(
                            MemberNames::ENTITY_ID => 2,
                            MemberNames::PATH => '1/2'
                        )
                    ),
                    RegistryKeys::EAV_USER_DEFINED_ATTRIBUTES => array(),
                    RegistryKeys::STORES => array(
                        'admin' => array(
                            MemberNames::STORE_ID => 0,
                            MemberNames::WEBSITE_ID => 0,
                            MemberNames::CODE => 'admin',
                            MemberNames::NAME => 'Admin'
                        ),
                        'default' => array(
                            MemberNames::STORE_ID => 1,
                            MemberNames::WEBSITE_ID => 1,
                            MemberNames::CODE => 'default',
                            MemberNames::NAME => 'Default Store View'
                        ),
                        'en_US' => array(
                            MemberNames::STORE_ID => 2,
                            MemberNames::WEBSITE_ID => 1,
                            MemberNames::CODE => 'en_US',
                            MemberNames::NAME => 'US Store'
                        )
                    ),
                    RegistryKeys::ENTITY_TYPES => array(
                        EntityTypeCodes::CATALOG_PRODUCT => array(
                            MemberNames::ENTITY_TYPE_ID => 4,
                            MemberNames::ENTITY_TYPE_CODE => EntityTypeCodes::CATALOG_PRODUCT
                        )
                    ),
                    RegistryKeys::CORE_CONFIG_DATA => array(
                        'default/0/web/seo/use_rewrites' => array(
                            'config_id' => 1,
                            'scope' => 'default',
                            'scope_id' => 0,
                            'path' => 'web/seo/use_rewrites',
                            'value' => 1
                        ),
                        'default/0/web/unsecure/base_url' => array(
                            'config_id' => 2,
                            'scope' => 'default',
                            'scope_id' => 0,
                            'path' => 'web/unsecure/base_url',
                            'value' => 'http://127.0.0.1/magento2-ee-2.1.7-sampledata/'
                        ),
                        'default/0/web/secure/base_url' => array(
                            'config_id' => 3,
                            'scope' => 'default',
                            'scope_id' => 0,
                            'path' => 'web/secure/base_url',
                            'value' => 'https://127.0.0.1/magento2-ee-2.1.7-sampledata/'
                        ),
                        'default/0/general/locale/code' => array(
                            'config_id' => 4,
                            'scope' => 'default',
                            'scope_id' => 0,
                            'path' => 'general/locale/code',
                            'value' => 'en_US'
                        ),
                        'default/0/web/secure/use_in_frontend' => array(
                            'config_id' => 5,
                            'scope' => 'default',
                            'scope_id' => 0,
                            'path' => 'web/secure/use_in_frontend',
                            'value' => null
                        ),
                        'default/0/web/secure/use_in_adminhtml' => array(
                            'config_id' => 6,
                            'scope' => 'default',
                            'scope_id' => 0,
                            'path' => 'web/secure/use_in_adminhtml',
                            'value' => null
                        ),
                        'default/0/fallback/on/default/level' => array(
                            'config_id' => 7,
                            'scope' => 'default',
                            'scope_id' => 0,
                            'path' => 'fallback/on/website/level',
                            'value' => 1001
                        ),
                        'websites/1/fallback/on/website/level' => array(
                            'config_id' => 8,
                            'scope' => 'websites',
                            'scope_id' => 1,
                            'path' => 'fallback/on/website/level',
                            'value' => 1002
                        )
                    )
                )
            )
        );
    }

    /**
     * Mock the system loggers.
     *
     * @return array The array with the system loggers
     */
    protected function getMockLoggers()
    {
        return new ArrayCollection(
            array(
                LoggerKeys::SYSTEM => $this->getMockBuilder(LoggerInterface::class)
                                           ->setMethods(get_class_methods(LoggerInterface::class))
                                           ->getMock()
            )
        );
    }

    /**
     * Mock the registry processor.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject The registry processor mock
     */
    protected function getMockRegistryProcessor()
    {
        return $this->getMockBuilder(RegistryProcessorInterface::class)
                    ->setMethods(get_class_methods(RegistryProcessorInterface::class))
                    ->getMock();
    }

    /**
     * Mock the configuration.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject The mock configuration
     */
    protected function getMockConfiguration(array $operationNames = array('add-update'))
    {

        // create the mock configuration instance
        $mockConfiguration = $this->getMockBuilder(ConfigurationInterface::class)
                                  ->setMethods(get_class_methods(ConfigurationInterface::class))
                                  ->getMock();

        // mock the necessary methods
        $mockConfiguration->expects($this->any())
                          ->method('getOperationNames')
                          ->willReturn($operationNames);

        // return the mock configuration
        return $mockConfiguration;
    }

    /**
     * Return's the execution context.
     *
     * @param string $entityTypeCode The entity type code of the execution context
     * @param string $magentoEdition The Magento edition of the execution context
     *
     * @return \PHPUnit_Framework_MockObject_MockObject The mock execution context
     */
    protected function getMockExecutionContext($entityTypeCode = EntityTypeCodes::CATALOG_PRODUCT, $magentoEdition = EditionNames::CE)
    {

        // create the mock execution context
        $mockExecutionContext = $this->getMockBuilder(ExecutionContextInterface::class)
                                     ->setMethods(get_class_methods(ExecutionContextInterface::class))
                                     ->getMock();

        // mock the methods
        $mockExecutionContext->expects($this->any())
                             ->method('getEntityTypeCode')
                             ->willReturn($entityTypeCode);
        $mockExecutionContext->expects($this->any())
                             ->method('getMagentoEdition')
                             ->willReturn($magentoEdition);

        // return the mock execution context
        return $mockExecutionContext;
    }

    /**
     * Return's the plugin configuration.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject The mock plugin configuration
     */
    protected function getMockPluginConfiguration()
    {

        // create the mock plugin configuration
        $mockPluginConfiguration = $this->getMockBuilder(PluginConfigurationInterface::class)
                                        ->setMethods(get_class_methods(PluginConfigurationInterface::class))
                                        ->getMock();

        // mock the necessary methods
        $mockPluginConfiguration->expects($this->any())
                                        ->method('getId')
                                        ->willReturn('import.plugin.subject');
        $mockPluginConfiguration->expects($this->any())
                                ->method('getExecutionContext')
                                ->willReturn($this->getMockExecutionContext());

        // return the mock plugin configuration
        return $mockPluginConfiguration;
    }

    /**
     * Mock the subject configuration.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject The mock subject configuration
     */
    protected function getMockSubjectConfiguration()
    {

        return $this->getMockBuilder(SubjectConfigurationInterface::class)
                    ->setMethods(get_class_methods(SubjectConfigurationInterface::class))
                    ->getMock();
    }

    /**
     * Mock the subject constructor args.
     *
     * @return array The subject constructor args
     */
    protected function getMockSubjectConstructorArgs()
    {

        // mock the registry processor
        $mockRegistryProcessor = $this->getMockRegistryProcessor();

        // mock the generator
        $mockGenerator = new CoreConfigDataUidGenerator();

        // mock the loggers
        $mockLoggers = $this->getMockLoggers();

        // mock the event emitter
        $mockEmitter = $this->getMockBuilder(EmitterInterface::class)
                            ->setMethods(\get_class_methods(EmitterInterface::class))
                            ->getMock();

        // mock the event emitter
        $mockLoader = $this->getMockBuilder(LoaderInterface::class)
            ->setMethods(\get_class_methods(LoaderInterface::class))
            ->getMock();

        // prepare the constructor arguments
        return array(
            $mockRegistryProcessor,
            $mockGenerator,
            $mockLoggers,
            $mockEmitter,
            $mockLoader
        );
    }

    /**
     * The class name of the subject we want to test.
     *
     * @return string The class name of the subject
     */
    abstract protected function getSubjectClassName();

    /**
     * Return's an array with method names that should also be mocked.
     *
     * @return array The array with the method names
     */
    abstract protected function getSubjectMethodsToMock();

    /**
     * Return's the subject instance we want to test.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject The subject instance
     */
    protected function getSubjectInstance(array $methodsToMock = array())
    {

        // initialize the default callback mappings
        $defaultCallbacks = array(
            'attribute_code' => array('import.test.callback-00.id')
        );

        // initialize the callback mappings
        $callbacks = array(
            array(
                'attribute_code'         => array('import.test.callback-01.id'),
                'another_attribute_code' => array('import.test.callback-02.id')
            )
        );

        // mock the date converter configuration
        $mockDateConverterConfiguration = $this->getMockBuilder(DateConverterConfigurationInterface::class)->getMock();
        $mockDateConverterConfiguration->expects($this->any())->method('getSourceDateFormat')->willReturn('n/d/y, g:i A');

        // create a mock configuration
        $mockConfiguration = $this->getMockConfiguration();
        // create a mock subject configuration
        $mockSubjectConfiguration = $this->getMockSubjectConfiguration();
        $mockSubjectConfiguration->expects($this->any())
            ->method('getConfiguration')
            ->willReturn($mockConfiguration);
        $mockSubjectConfiguration->expects($this->any())
            ->method('getCallbacks')
            ->willReturn($callbacks);
        $mockSubjectConfiguration->expects($this->any())
            ->method('getHeaderMappings')
            ->willReturn(array());
        $mockSubjectConfiguration->expects($this->any())
            ->method('getFrontendInputCallbacks')
            ->willReturn(array());
        $mockSubjectConfiguration->expects($this->any())
            ->method('getDateConverter')
            ->willReturn($mockDateConverterConfiguration);
        $mockSubjectConfiguration->expects($this->any())
            ->method('getPluginConfiguration')
            ->willReturn($this->getMockPluginConfiguration());


        // initialize the abstract subject that has to be tested
        $abstractSubject = $this->getMockBuilder($this->getSubjectClassName())
            ->setConstructorArgs($this->getMockSubjectConstructorArgs())
            ->setMethods(
                array_merge(
                    $this->getSubjectMethodsToMock(),
                    $methodsToMock
                )
            )
            ->getMockForAbstractClass();

        // mock the subject's methods
        $abstractSubject->expects($this->any())
            ->method('getDefaultCallbackMappings')
            ->willReturn($defaultCallbacks);
        $abstractSubject->expects($this->any())
            ->method('getExecutionContext')
            ->willReturn($this->getMockExecutionContext());

        // mock the getAttribute() method
        $abstractSubject->getRegistryProcessor()
            ->expects($this->any())
            ->method('getAttribute')
            ->willReturn($this->getMockGlobalData());

        // set the mock configuration instance
        $abstractSubject->setConfiguration($mockSubjectConfiguration);

        // return the abstract subject
        return $abstractSubject;
    }
}
