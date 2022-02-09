<?php

/**
 * TechDivision\Import\Callbacks\PipeDelimiterSkuRelationsValidatorCallback
 *
 * PHP version 7
 *
 * @author    Kenza Yamlahi <k.yamlahi@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Callbacks;

use TechDivision\Import\Exceptions\InvalidDataException;
use TechDivision\Import\Utils\ColumnKeys;
use TechDivision\Import\Utils\RegistryKeys;

/**
 * A callback implementation that validates the a list of values.
 *
 * @author    Kenza Yamlahi <k.yamlahi@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class PipeDelimiterSkuRelationsValidatorCallback extends CommaDelimiterSkuRelationsValidatorCallback
{

    /**
     * @param string $value the value to explode
     * @return string
     */
    protected function explodeDetailsFromValue($value)
    {
        $skuValue = $this->getSubject()->explode($value, ',');
        foreach ($skuValue as $subValue) {
            if (str_starts_with($subValue, ColumnKeys::SKU)) {
                list(, $value) = $this->getSubject()->explode($subValue, '=');
                return $value;
            }
        }
        // Nothing found? Return origin
        return $value;
    }

    /**
     * @return string
     */
    protected function getAttributeValueDelimiter()
    {
        return '|';
    }
}
