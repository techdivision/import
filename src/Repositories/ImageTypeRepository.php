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

use TechDivision\Import\Utils\MemberNames;
use TechDivision\Import\Utils\SqlStatementKeys;
use TechDivision\Import\Dbal\Repositories\AbstractRepository;

/**
 * Repository implementation to load image type data.
 *
 * @author    Reinhard Hampl <r.hampl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ImageTypeRepository extends AbstractRepository implements ImageTypeRepositoryInterface
{

    /**
     * The prepared statement to load the default store.
     *
     * @var \PDOStatement
     */
    protected $imageTypesByEntityTypeCodeAndFrontendInputStmt;

    /**
     * The mapping for the image/thumbnail image types
     *
     * @var array
     */
    protected $defaultMappings = array(
        'image'     => 'base_image',
        'thumbnail' => 'thumbnail_image'
    );

    /**
     * Initializes the repository's prepared statements.
     *
     * @return void
     */
    public function init()
    {

        // initialize the prepared statements
        $this->imageTypesByEntityTypeCodeAndFrontendInputStmt =
            $this->getConnection()->prepare($this->loadStatement(SqlStatementKeys::IMAGE_TYPES));
    }

    /**
     * Return's an array with all available image types for the passed entity type code
     * and frontend input type with the link type code as key.
     *
     * @return array The available image types
     */
    public function findAll()
    {

        // initialize the result array
        $result = array();

        // load and the image types from the EAV attribute table
        $this->imageTypesByEntityTypeCodeAndFrontendInputStmt->execute();

        // fetch the image types with the passed parameters
        if ($imageTypes = $this->imageTypesByEntityTypeCodeAndFrontendInputStmt->fetchAll(\PDO::FETCH_ASSOC)) {
            // iterate over the image types found
            foreach ($imageTypes as $imageType) {
                $attributeCode = $imageType[MemberNames::ATTRIBUTE_CODE];
                // map the default image types
                if (isset($this->defaultMappings[$attributeCode])) {
                    $attributeCode = $this->defaultMappings[$attributeCode];
                }

                // add the (mapped) image type
                $result[$attributeCode] = sprintf('%s_label', $attributeCode);
            }
        }

        // return the result
        return $result;
    }
}
