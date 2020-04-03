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
interface SubjectConfigurationInterface extends ParamsConfigurationInterface, ImportAdapterAwareConfigurationInterface, ExportAdapterAwareConfigurationInterface
{

    /**
     * Return's the multiple field delimiter character to use, default value is comma (,).
     *
     * @return string The multiple field delimiter character
     */
    public function getMultipleFieldDelimiter();

    /**
     * Return's the multiple value delimiter character to use, default value is comma (|).
     *
     * @return string The multiple value delimiter character
     */
    public function getMultipleValueDelimiter();

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
     * Return's the subject's unique DI identifier
     *
     * @return string The subject's unique DI identifier
     */
    public function getId();

    /**
     * Return's the subject's name or the ID, if the name is NOT set.
     *
     * @return string The subject's name
     * @see \TechDivision\Import\Configuration\SubjectConfigurationInterface::getId()
     */
    public function getName();

    /**
     * Return's the reference to the configuration instance.
     *
     * @return \TechDivision\Import\ConfigurationInterface The configuration instance
     */
    public function getConfiguration();

    /**
     * Return's the reference to the parent plugin configuration instance.
     *
     * @return \TechDivision\Import\Configuration\PluginConfigurationInterface The parent plugin configuration instance
     */
    public function getPluginConfiguration();

    /**
     * Return's the prefix for the import files.
     *
     * @return string The prefix
     */
    public function getPrefix();

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
     * Return's the array with the subject's frontend input callbacks.
     *
     * @return array The subject's frontend input callbacks
     */
    public function getFrontendInputCallbacks();

    /**
     * Queries whether or not that the subject needs an OK file to be processed.
     *
     * @return boolean TRUE if the subject needs an OK file, else FALSE
     */
    public function isOkFileNeeded();

    /**
     * Queries whether or not the subject should create a .imported flagfile
     *
     * @return boolean TRUE if subject has to create the .imported flagfile, else FALSE
     */
    public function isCreatingImportedFile();

    /**
     * Return's the import adapter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\ImportAdapterConfigurationInterface The import adapter configuration instance
     */
    public function getImportAdapter();

    /**
     * Return's the export adapter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\ExportAdapterConfigurationInterface The export adapter configuration instance
     */
    public function getExportAdapter();

    /**
     * Return's the filesystem adapter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FilesystemAdapterConfigurationInterface The filesystem adapter configuration instance
     */
    public function getFilesystemAdapter();

    /**
     * Return's the file resolver configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FileResolverConfigurationInterface The file resolver configuration instance
     */
    public function getFileResolver();

    /**
     * Return's the file writer configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\FileWriterConfigurationInterface The file writer configuration instance
     */
    public function getFileWriter();

    /**
     * Return's the number converter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\NumberConverterConfigurationInterface The number converter configuration instance
     */
    public function getNumberConverter();

    /**
     * Return's the date converter configuration instance.
     *
     * @return \TechDivision\Import\Configuration\Subject\DateConverterConfigurationInterface The date converter configuration instance
     */
    public function getDateConverter();

    /**
     * The array with the subject's custom header mappings.
     *
     * @return array The custom header mappings
     */
    public function getHeaderMappings();

    /**
     * The array with the subject's custom image types.
     *
     * @return array The custom image types
     */
    public function getImageTypes();

    /**
     * Return's the array with the configured listeners.
     *
     * @return array The array with the listeners
     */
    public function getListeners();

    /**
     * Return's the execution context configuration for the actualy plugin configuration.
     *
     * @return \TechDivision\Import\ExecutionContextInterface The execution context to use
     */
    public function getExecutionContext();

    /**
     * Load the default values from the configuration.
     *
     * @return array The array with the default values
     */
    public function getDefaultValues();

    /**
     * Return's the full opration name, which consists of the Magento edition, the entity type code and the operation name.
     *
     * @param string $separator The separator used to seperate the elements
     *
     * @return string The full operation name
     */
    public function getFullOperationName($separator = '/');
}
