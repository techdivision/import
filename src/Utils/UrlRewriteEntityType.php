<?php

/**
 * TechDivision\Import\Utils\UrlRewriteEntityType
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Utils;

/**
 * The enum with the entity types for the URL rewrite handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class UrlRewriteEntityType implements EnumInterface
{

    /**
     * The URL rewrite entity type `product`.
     *
     * @var string
     */
    const PRODUCT = 'product';

    /**
     * The URL rewrite entity type `category`.
     *
     * @var string
     */
    const CATEGORY = 'category';
    /**
     * The URL rewrite entity type `cms-page`.
     *
     * @var string
     */
    const CMS_PAGE = 'cms-page';

    /**
     * The array with the valid URL rewrite entity types.
     *
     * @var array
     */
    private $types = array(
        UrlRewriteEntityType::PRODUCT,
        UrlRewriteEntityType::CATEGORY,
        UrlRewriteEntityType::CMS_PAGE
    );

    /**
     * The URL rewrite entity type value.
     *
     * @var string
     */
    private $value;

    /**
     * Initializes the enum with the passed value.
     *
     * @param string $value The enum's value to use
     */
    public function __construct(string $value = UrlRewriteEntityType::PRODUCT)
    {
        if ($this->isValid($value)) {
            $this->value = $value;
        } else {
            throw new \InvalidArgumentException(sprintf('Value "%s" not a const in enum "%s"', $value, __CLASS__));
        }
    }

    /**
     * Query whether or not the passed value is valid.
     *
     * @param string $value Thevalue to query for
     *
     * @return boolean TRUE if the value is valid, else FALSE
     * @see \TechDivision\Import\Utils\EnumInterface::isValid()
     */
    public function isValid($value) : bool
    {
        return in_array($value, $this->types, true);
    }

    /**
     * Query whether or not the actual instance has the passed value.
     *
     * @param string $value The value to query for
     *
     * @return bool TRUE if the instance equals the passed value, else FALSE
     */
    public function equals($value) : bool
    {
        return $this->value === $value;
    }

    /**
     * Return's the enum's value.
     *
     * @return string The enum's value
     * @see \TechDivision\Import\Utils\EnumInterface::__toString()
     */
    public function __toString() : string
    {
        return (string) $this->value;
    }
}
