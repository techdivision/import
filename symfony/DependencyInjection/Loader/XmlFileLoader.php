<?php

/**
 * Selects the XML loader matching the installed symfony/dependency-injection version.
 *
 * symfony/dependency-injection 6.4 to 7.x ship their own XmlFileLoader, which, like
 * the copy, disables the alias auto registration for singly implemented interfaces,
 * so it is used wherever it exists. Symfony 8 removed it, so only there the copy of
 * the Symfony 7 loader (Symfony7XmlFileLoader) is used. The copy depends on
 * FileLoader::loadExtensionConfigs(), which does not exist before Symfony 7.1, and
 * therefore must not be used on older versions (fatal error on Symfony 6.4).
 *
 * @link https://github.com/techdivision/import
 */

namespace TechDivision\Import\DependencyInjection\Loader;

use Symfony\Component\DependencyInjection\Loader\XmlFileLoader as SymfonyXmlFileLoader;

if (class_exists(SymfonyXmlFileLoader::class)) {
    class_alias(SymfonyXmlFileLoader::class, XmlFileLoader::class);
} else {
    class_alias(Symfony7XmlFileLoader::class, XmlFileLoader::class);
}
