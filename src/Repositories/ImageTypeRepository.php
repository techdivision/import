<?php

/**
 * TechDivision\Import\Repositories\ImageTypeRepository
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Reinhard Hampl <r.hampl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Repositories;

use TechDivision\Import\Utils\ImageTypeKeys;

/**
 * Repository implementation to load image type data.
 *
 * @author    Reinhard Hampl <r.hampl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ImageTypeRepository extends AbstractRepository implements LinkTypeRepositoryInterface
{

    /**
     * The array with the Magento 2 default image types and their label columns. The key equals to the attribute name.
     *
     * @var array
     */
    protected $imageTypes = array(
        'image' => array(
            ImageTypeKeys::IMAGE => 'base_image',
            ImageTypeKeys::IMAGE_LABEL => 'base_image_label'
        ),
        'small_image' => array(
            ImageTypeKeys::IMAGE => 'small_image',
            ImageTypeKeys::IMAGE_LABEL => 'small_image_label'
        ),
        'swatch_image' => array(
            ImageTypeKeys::IMAGE => 'swatch_image',
            ImageTypeKeys::IMAGE_LABEL => 'swatch_image_label'
        ),
        'thumbnail' => array(
            ImageTypeKeys::IMAGE => 'thumbnail_image',
            ImageTypeKeys::IMAGE_LABEL => 'thumbnail_image_label'
        )
    );

    /**
     * The cache for the query results.
     *
     * @var array
     */
    protected $cache = array();

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {
    }

    /**
     * Extends the global image types. Override this function in case
     * the image types has to be extended.
     *
     * @return array The array with the extended image types
     */
    public function extendsImageTypesFromDatabase()
    {
        return array();
    }

    /**
     * Return's an array with all available image types with the link type code as key.
     *
     * @return array The available image types
     */
    public function findAll()
    {

        // query whether or not we've already loaded the value
        if (!isset($this->cache[__METHOD__])) {
            // load the extended image types and append the image types to the cache
            $this->cache[__METHOD__] =  array_merge($this->imageTypes, $this->extendsImageTypesFromDatabase());
        }

        // return the image types from the cache
        return $this->cache[__METHOD__];
    }
}
