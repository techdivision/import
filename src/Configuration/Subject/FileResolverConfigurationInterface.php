<?php

/**
 * TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface
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

namespace TechDivision\Import\Configuration\Subject;

/**
 * The interface for a file resolver's configuration.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FileResolverConfigurationInterface
{

    /**
     * Returns the file resolver's unique DI identifier.
     *
     * @return string The file resolver's unique DI identifier
     */
    public function getId();

    /**
     * Returns the prefix/meta sequence for the import files.
     *
     * @return string The prefix
     */
    public function getPrefix();

    /**
     * Returns the filename/meta sequence of the import files.
     *
     * @return string The suffix
     */
    public function getFilename();

    /**
     * Returns the counter/meta sequence of the import files.
     *
     * @return string The suffix
     */
    public function getCounter();

    /**
     * Returns the suffix for the import files.
     *
     * @return string The suffix
     */
    public function getSuffix();

    /**
     * Return's the suffix for the OK file.
     *
     * @return string The OK file suffix
     */
    public function getOkFileSuffix();

    /**
     * Returns the delement separator char.
     *
     *  @return string The element separator char
     */
    public function getElementSeparator();

    /**
     * Returns the elements the filenames consists of.
     *
     * @return array The array with the filename elements
     */
    public function getPatternElements();
}
