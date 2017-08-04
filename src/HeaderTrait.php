<?php

/**
 * TechDivision\Import\HeaderTrait
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

namespace TechDivision\Import;

/**
 * A trait that provides header handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
trait HeaderTrait
{

    /**
     * Contain's the column names from the header line.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Mappings for attribute code => CSV column header.
     *
     * @var array
     */
    protected $headerMappings = array();

    /**
     * Set's the array containing header row.
     *
     * @param array $headers The array with the header row
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }

    /**
     * Return's the array containing header row.
     *
     * @return array The array with the header row
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Return's the header mappings for the actual entity.
     *
     * @return array The header mappings
     */
    public function getHeaderMappings()
    {
        return $this->headerMappings;
    }

    /**
     * Queries whether or not the header with the passed name is available.
     *
     * @param string $name The header name to query
     *
     * @return boolean TRUE if the header is available, else FALSE
     */
    public function hasHeader($name)
    {
        return isset($this->headers[$this->mapAttributeCodeByHeaderMapping($name)]);
    }

    /**
     * Return's the header value for the passed name.
     *
     * @param string $name The name of the header to return the value for
     *
     * @return mixed The header value
     * @throws \InvalidArgumentException Is thrown, if the header with the passed name is NOT available
     */
    public function getHeader($name)
    {

        // map column => attribute name
        $name = $this->mapAttributeCodeByHeaderMapping($name);

        // query whether or not, the header is available
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }

        // throw an exception, if not
        throw new \InvalidArgumentException(sprintf('Header %s is not available', $name));
    }

    /**
     * Add's the header with the passed name and position, if not NULL.
     *
     * @param string $name The header name to add
     *
     * @return integer The new headers position
     */
    public function addHeader($name)
    {

        // add the header
        $this->headers[$name] = $position = sizeof($this->headers);

        // return the new header's position
        return $position;
    }

    /**
     * Map the passed attribute code, if a header mapping exists and return the
     * mapped mapping.
     *
     * @param string $attributeCode The attribute code to map
     *
     * @return string The mapped attribute code, or the original one
     */
    public function mapAttributeCodeByHeaderMapping($attributeCode)
    {

        // load the header mappings
        $headerMappings = $this->getHeaderMappings();

        // query weather or not we've a mapping, if yes, map the attribute code
        if (isset($headerMappings[$attributeCode])) {
            $attributeCode = $headerMappings[$attributeCode];
        }

        // return the (mapped) attribute code
        return $attributeCode;
    }
}
