<?php

/**
 * TechDivision\Import\Loaders\NoStrictValidationLoader
 *
 * PHP version 7
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2021 GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

/**
 * Specific loader for the validation data.
 *
 * @author    MET <met@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class NoStrictValidationLoader implements LoaderInterface
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
        if (is_array($novStrictValidations = $this->getLoader()->load())) {
            foreach ($novStrictValidations as $filename => $attributes) {
                foreach ($attributes as $line => $messages) {
                    foreach ($messages as $attributeCode => $message) {
                        $data[] = array(
                            'File'   => $filename,
                            'Line'   => $line,
                            'Operation' => $attributeCode,
                            'Message'  => $message
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
