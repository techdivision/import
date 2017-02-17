<?php

/**
 * TechDivision\Import\Configuration\SubjectConfigurationInterface
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

namespace TechDivision\Import\Configuration;

/**
 * Interface for the subject configuration implementation.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
interface SubjectConfigurationInterface
{

    /**
     * Return's the delimiter character to use, default value is comma (,).
     *
     * @return string The delimiter character
     */
    public function getDelimiter();

    /**
     * The enclosure character to use, default value is double quotation (").
     *
     * @return string The enclosure character
     */
    public function getEnclosure();

    /**
     * The escape character to use, default value is backslash (\).
     *
     * @return string The escape character
     */
    public function getEscape();

    /**
     * The file encoding of the CSV source file, default value is UTF-8.
     *
     * @return string The charset used by the CSV source file
     */
    public function getFromCharset();

    /**
     * The file encoding of the CSV targetfile, default value is UTF-8.
     *
     * @return string The charset used by the CSV target file
     */
    public function getToCharset();

    /**
     * The file mode of the CSV target file, either one of write or append, default is write.
     *
     * @return string The file mode of the CSV target file
     */
    public function getFileMode();

    /**
     * Queries whether or not strict mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if strict mode is enabled, else FALSE
     */
    public function isStrictMode();

    /**
     * Return's the subject's class name.
     *
     * @return string The subject's class name
     */
    public function getClassName();

    /**
     * Return's the subject's processor factory type to use.
     *
     * @return string The subject's processor factory type
     */
    public function getProcessorFactory();

    /**
     * Return's the utility class with the SQL statements to use.
     *
     * @return string The utility class name
     */
    public function getUtilityClassName();

    /**
     * Return's the reference to the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    public function getConfiguration();

    /**
     * Return's the prefix for the import files.
     *
     * @return string The prefix
     */
    public function getPrefix();

    /**
     * Return's the subject's source date format to use.
     *
     * @return string The source date format
     */
    public function getSourceDateFormat();

    /**
     * Return's the source directory that has to be watched for new files.
     *
     * @return string The source directory
     */
    public function getSourceDir();

    /**
     * Return's the target directory with the files that has been imported.
     *
     * @return string The target dir
     */
    public function getTargetDir();

    /**
     * Queries whether or not debug mode is enabled or not, default is TRUE.
     *
     * @return boolean TRUE if debug mode is enabled, else FALSE
     */
    public function isDebugMode();

    /**
     * Return's the array with the subject's observers.
     *
     * @return array The subject's observers
     */
    public function getObservers();

    /**
     * Return's the array with the subject's callbacks.
     *
     * @return array The subject's callbacks
     */
    public function getCallbacks();

    /**
     * Return's the array with the params.
     *
     * @return array The params
     */
    public function getParams();

    /**
     * Query whether or not the param with the passed name exists.
     *
     * @param string $name The name of the param to be queried
     *
     * @return boolean TRUE if the requested param exists, else FALSE
     */
    public function hasParam($name);

    /**
     * Return's the param with the passed name.
     *
     * @param string $name         The name of the param to return
     * @param mixed  $defaultValue The default value if the param doesn't exists
     *
     * @return string The requested param
     * @throws \Exception Is thrown, if the requested param is not available
     */
    public function getParam($name, $defaultValue = null);

    /**
     * Queries whether or not that the subject needs an OK file to be processed.
     *
     * @return boolean TRUE if the subject needs an OK file, else FALSE
     */
    public function isOkFileNeeded();
}
