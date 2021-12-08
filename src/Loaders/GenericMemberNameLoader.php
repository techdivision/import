<?php

/**
 * TechDivision\Import\Loaders\GenericMemberNameLoader
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Loaders;

/**
 * Generic loader iplementation that loads the values of the member with
 * the given name of the entities laoded by the parent loader instance.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2021 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class GenericMemberNameLoader implements LoaderInterface
{

    /**
     * The array with the member values.
     *
     * @var array
     */
    protected $values;

    /**
     * Construct that initializes the iterator with the parent iterator instance used to load the enities.
     *
     * @param \TechDivision\Import\Loaders\LoaderInterface $entityLoader The entity loader instance
     * @param string                                       $memberName   The name of the entity's member to load the values for
     *
     * @throws \InvalidArgumentException Is thrown, if one of the loaded entities doesn't contain the member with the given name
     */
    public function __construct(LoaderInterface $entityLoader, string $memberName)
    {

        // load the entities
        $entities = $entityLoader->load();

        // load the member values
        foreach ($entities as $entity) {
            if (isset($entity[$memberName])) {
                $this->values[] = $entity[$memberName];
            } else {
                throw new \InvalidArgumentException(sprintf('Mandatory member "%s" not available', $memberName));
            }
        }
    }

    /**
     * Loads and returns data.
     *
     * @return \ArrayAccess The array with the data
     */
    public function load()
    {
        return $this->values;
    }
}
