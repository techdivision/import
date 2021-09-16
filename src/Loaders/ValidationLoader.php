<?php

/**
 * TechDivision\Import\Loaders\ValidationLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

/**
 * Specific loader for the validation data.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2019 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class ValidationLoader implements LoaderInterface
{

    /**
     * The loader instance used to load the validations from the registry.
     *
     * @var \TechDivision\Import\Loaders\LoaderInterface
     */
    protected $loader;

    /**
     * Construct that initializes the iterator with the loader instance.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $loader The loader instance
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Loads and returns data.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {

        // initialize the array for the assembled validations
        $data = array();

        // load the validations from the wrapped loader
        if (is_array($validations = $this->getLoader()->load())) {
            foreach ($validations as $filename => $attributes) {
                foreach ($attributes as $line => $errors) {
                    foreach ($errors as $attributeCode => $message) {
                        $data[] = array(
                            'File'   => $filename,
                            'Line'   => $line,
                            'Column' => $attributeCode,
                            'Error'  => $message
                        );
                    }
                }
            }
        }

        // return the assembled validation data
        return $data;
    }

    /**
     * Return's the loader instance
     *
     * @return \TechDivision\Import\Loaders\LoaderInterface The loader instance
     */
    protected function getLoader()
    {
        return $this->loader;
    }
}
