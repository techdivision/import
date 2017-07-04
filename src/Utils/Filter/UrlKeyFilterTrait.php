<?php

/**
 * TechDivision\Import\Utils\Filter\UrlKeyFilterTrait
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
 * @link      https://github.com/techdivision/import-category
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils\Filter;

/**
 * Trait that provides string to URL key convertion functionality.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
     * @return \TechDivision\Import\Product\Utils\ConvertLiteralUrl The URL key filter
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
