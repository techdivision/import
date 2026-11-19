<?php

/**
 * TechDivision\Import\DependencyInjection\Loader\XmlFileLoaderTest
 *
 * PHP version 8
 *
 * @author    TechDivision GmbH <info@techdivision.com>
 * @copyright 2026 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\DependencyInjection\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader as SymfonyXmlFileLoader;

/**
 * Test class for the XML file loader selection.
 *
 * @author    TechDivision GmbH <info@techdivision.com>
 * @copyright 2026 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class XmlFileLoaderTest extends TestCase
{

    /**
     * Tests that the loader of the installed Symfony version is used if it exists
     * and that the copy of the Symfony 7 loader is used otherwise.
     *
     * @return void
     */
    public function testLoaderMatchesInstalledSymfonyVersion(): void
    {
        $expected = class_exists(SymfonyXmlFileLoader::class) ? SymfonyXmlFileLoader::class : Symfony7XmlFileLoader::class;

        $this->assertTrue(is_a(XmlFileLoader::class, $expected, true));
        $this->assertSame($expected, (new \ReflectionClass(XmlFileLoader::class))->getName());
    }

    /**
     * Tests that the services.xml of the bundle can be loaded without a fatal error.
     *
     * @return void
     */
    public function testLoadsServicesXml(): void
    {
        $container = new ContainerBuilder();
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__, 4) . '/symfony/Resources/config'));
        $loader->load('services.xml');

        $this->assertNotEmpty($container->getDefinitions());
    }
}
