<?php

/**
 * TechDivision\Import\Subjects\FileResolver\FileResolverInterface
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

namespace TechDivision\Import\Subjects\FileResolver;

use TechDivision\Import\Adapter\FilesystemAdapterInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Interface for all file resolver implementations.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface FileResolverInterface
{

    /**
     * Sets the subject configuration instance.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     *
     * @return void
     */
    public function setSubjectConfiguration(SubjectConfigurationInterface $subjectConfiguration);

    /**
     * Returns the subject configuration instance.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration
     */
    public function getSubjectConfiguration();

    /**
     * Set's the filesystem adapter instance.
     *
     * @param \TechDivision\Import\Adapter\FilesystemAdapterInterface $filesystemAdapter
     */
    public function setFilesystemAdapter(FilesystemAdapterInterface $filesystemAdapter);

    /**
     * Return's the filesystem adapter instance.
     *
     * @return \TechDivision\Import\Adapter\FilesystemAdapterInterface The filesystem adapter instance
     */
    public function getFilesystemAdapter();

    /**
     * Returns the number of matches found.
     *
     * @return integer The number of matches
     */
    public function countMatches();

    /**
     * Returns the matches.
     *
     * @return array The array with the matches
     */
    public function getMatches();

    /**
     * Adds the passed match to the array with the matches.
     *
     * @param string $match The match itself
     *
     * @return void
     */
    public function addMatch(array $match);

    /**
     * Returns the match with the passed name.
     *
     * @param string $name The name of the match to return
     *
     * @return string|null The match itself
     */
    public function getMatch($name);

    /**
     * Resets the file resolver to parse another source directory for new files.
     *
     * @return void
     */
    public function reset();

    /**
     * Returns the OK file suffix to use.
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
     * Returns the elements the filenames consists of, converted to lowercase.
     *
     * @return array The array with the filename elements
     */
    public function getPatternKeys();

    /**
     * Returns the values to create the regex pattern from.
     *
     * @param array|null $patternKeys The pattern keys used to load the pattern values
     *
     * @return array The array with the pattern values
     */
    public function resolvePatternValues(array $patternKeys = null);

    /**
     * Prepares and returns the pattern for the regex to load the files from the
     * source directory for the passed subject.
     *
     * @param array|null  $patternKeys      The pattern keys used to load the pattern values
     * @param string|null $suffix           The suffix used to prepare the regular expression
     * @param string|null $elementSeparator The element separator used to prepare the regular expression
     *
     * @return string The prepared regex pattern
     */
    public function preparePattern(array $patternKeys = null, string $suffix = null, string $elementSeparator = null) : string;

    /**
     * Loads the files from the source directory and return's them sorted.
     *
     * @param string $serial The unique identifier of the actual import process
     *
     * @return array The array with the files matching the subjects suffix
     * @throws \Exception Is thrown, when the source directory is NOT available
     */
    public function loadFiles($serial);
}
