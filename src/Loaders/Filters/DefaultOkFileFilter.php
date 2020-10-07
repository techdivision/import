<?php

/**
 * TechDivision\Import\Loaders\Filters\DefaultOkFileFilter
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
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders\Filters;

use TechDivision\Import\Configuration\SubjectConfigurationInterface;

/**
 * Factory for file writer instances.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class DefaultOkFileFilter implements FilterInterface
{

    /**
     * The subject configuration containing the file suffix to use for filtering purposes.
     *
     * @var \TechDivision\Import\Configuration\SubjectConfigurationInterface
     */
    private $subjectConfiguration;

    /**
     * Initializes the filter with the passed subject configuration.
     *
     * @param \TechDivision\Import\Configuration\SubjectConfigurationInterface $subjectConfiguration The subject configuration
     */
    public function __construct(SubjectConfigurationInterface $subjectConfiguration)
    {
        $this->subjectConfiguration = $subjectConfiguration;
    }

    /**
     * Return's the subject configuration instance.
     *
     * @return \TechDivision\Import\Configuration\SubjectConfigurationInterface The subject configuration instance
     */
    private function getSubjectConfiguration() : SubjectConfigurationInterface
    {
        return $this->subjectConfiguration;
    }

    /**
     * Return's the suffix configured in the file resolver of the
     * given subject configuration.
     *
     * @return string The suffix used for filter purposes
     */
    private function getSuffix() : string
    {
        return $this->getSubjectConfiguration()->getFileResolver()->getSuffix();
    }

    /**
     * The filter's unique name.
     *
     * @return string The unique name
     */
    public function getName() : string
    {
        return (string) DefaultOkFileFilter::class;
    }

    /**
     * Return's the flag used to define what will be passed to the callback invoked
     * by the `array_filter()` method.
     *
     * @return int The flag
     */
    public function getFlag() : int
    {
        return ARRAY_FILTER_USE_BOTH;
    }

    /**
     * This is the callback method that will be called by the invoking `array_filter` function.
     *
     * @param array  $v The array with files that has to imported and that should be matches against the passed .OK filename
     * @param string $k The key name, which has to be the .OK filename in this case
     *
     * @return bool TRUE if the value should be in the array, else FALSE
     * @todo Making pattern creation for .OK and import files as well as the suffix more generic
     */
    public function __invoke($v, $k) : bool
    {

        // iterate over the  array with the passed .OK filenames and the matching
        // files that has to be imported to figure out if they match given pattern
        foreach ($v as $f) {
            // initialize the array for the matches
            $matches = array();
            // prepare the pattern
            $pattern = sprintf('/^(?<prefix>.*)_(?<filename>.*)_.*\.%s$/', $this->getSuffix());
            // try to match the pattern against the import file and the .OK file
            if (preg_match($pattern, $f, $matches)) {
                if (preg_match(sprintf('/^.*\/%s_%s\.ok$/', $matches['prefix'], $matches['filename']), $k)) {
                    return true;
                }
            }
        }

        // return FALSE, if the pattern doesn't match
        return false;
    }
}
