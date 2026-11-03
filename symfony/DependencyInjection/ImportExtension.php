<?php

/**
 * TechDivision\Import\DependencyInjection\ImportExtension
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader as SymfonyXmlFileLoader;
use TechDivision\Import\DependencyInjection\Loader\XmlFileLoader;

/**
 * The symfony extension implementation for the M2IF import library.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ImportExtension extends Extension
{
    /**
     * Load's the bundles DI configuration.
     *
     * @param array                                                   $configs   The array with the configuration
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container The container instance
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        // use the XML loader shipped with symfony/dependency-injection where available (6.4 and 7.x),
        // symfony 8 removed it, so the copy of the 7.x loader has to be used there
        $loaderClass = class_exists(SymfonyXmlFileLoader::class) ? SymfonyXmlFileLoader::class : XmlFileLoader::class;
        $loader = new $loaderClass($container, new FileLocator(dirname(__DIR__) . '/Resources/config'));
        $loader->load('services.xml');
    }
}
