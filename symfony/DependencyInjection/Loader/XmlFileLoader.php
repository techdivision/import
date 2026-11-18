<?php

/**
 * TechDivision\Import\DependencyInjection\Loader\XmlFileLoader
 *
 * PHP version 8
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2026 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader as SymfonyXmlFileLoader;

/*
 * Selects the XML loader matching the installed symfony/dependency-injection version.
 *
 * Symfony 8 removed the XmlFileLoader, so we ship our own copy (Symfony7XmlFileLoader).
 * That copy depends on FileLoader internals (typed properties, loadExtensionConfig()
 * and loadExtensionConfigs()) which only exist from Symfony 7.1 on. Older versions,
 * e.g. Symfony 6.4 as pinned by Magento 2.4.7, still ship their own XmlFileLoader,
 * which already disables the alias auto registration for singly implemented
 * interfaces, so we use that one there.
 */
if (method_exists(FileLoader::class, 'loadExtensionConfigs')) {
    class_alias(Symfony7XmlFileLoader::class, XmlFileLoader::class);
} else {
    class_alias(SymfonyXmlFileLoader::class, XmlFileLoader::class);
}
