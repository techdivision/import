<?php

/**
 * TechDivision\Import\Plugins\CreateOkFilesPlugin
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Configuration\SubjectConfigurationInterface;
use TechDivision\Import\Subjects\FileWriter\FileWriterFactoryInterface;

/**
 * Plugin that creates .OK files for the .CSV files found in the actual source directory.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class CreateOkFilesPlugin extends AbstractPlugin
{

    /**
     * The file writer factory instance.
     *
     * @var \TechDivision\Import\Subjects\FileWriter\FileWriterFactoryInterface
     */
    protected $fileWriterFactory;

    /**
     *
     * @param \TechDivision\Import\ApplicationInterface                           $application       The application instance
     * @param \TechDivision\Import\Subjects\FileWriter\FileWriterFactoryInterface $fileWriterFactory The file writer factory instance
     */
    public function __construct(
        ApplicationInterface $application,
        FileWriterFactoryInterface $fileWriterFactory
    ) {

        // set the passed file writer factory instance
        $this->fileWriterFactory = $fileWriterFactory;

        // pass the application to the parent constructor
        parent::__construct($application);
    }

    /**
     * Return's the file writer factory instance.
     *
     * @return \TechDivision\Import\Subjects\FileWriter\FileWriterFactoryInterface The file writer factory instance
     */
    protected function getFileWriterFactory() : FileWriterFactoryInterface
    {
        return $this->fileWriterFactory;
    }

    /**
     * Process the plugin functionality.
     *
     * @return void
     * @throws \Exception Is thrown, if the plugin can not be processed
     */
    public function process()
    {

        // initialize the counter for the CSV files
        $okFilesCreated = 1;

        // initialize the subject filter we want to use
        $filters = array(function (SubjectConfigurationInterface $subject) {
            return $subject->isOkFileNeeded();
        });

        // load the matching subjects (only that one, that needs an .OK file)
        $subjects = $this->getConfiguration()->getSubjects($filters);

        // create the .OK files for that subjects
        foreach ($subjects as $subject) {
            $okFilesCreated += $this->getFileWriterFactory()->createFileWriter($subject)->createOkFiles($this->getSerial());
        }

        // query whether or not we've found any CSV files
        if ($okFilesCreated === 0) {
            throw new \Exception(sprintf('Can\'t find any CSV files in source directory "%s"', $this->getSourceDir()));
        }
    }
}
