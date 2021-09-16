<?php

/**
 * TechDivision\Import\Utils\Filter\UrlKeyFilterTrait
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils\Filter;

/**
 * Trait that provides string to URL key convertion functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */
trait UrlKeyFilterTrait
{

    /**
     * The URL filter instance.
     *
     * @var \Zend\Filter\FilterInterface
     */
    protected $convertLiteralUrlFilter;

    /**
     * Initialize's and return's the URL key filter.
     *
     * @return \Zend\Filter\FilterInterface The URL key filter
     */
    protected function getConvertLiteralUrlFilter()
    {
        return $this->convertLiteralUrlFilter;
    }

    /**
     * Convert's the passed string into a valid URL key.
     *
     * @param string $string The string to be converted, e. g. the product name
     *
     * @return string The converted string as valid URL key
     */
    protected function convertNameToUrlKey($string)
    {
        return $this->getConvertLiteralUrlFilter()->filter($string);
    }
}
