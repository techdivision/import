<?php

/**
 * TechDivision\Import\Adapter\Goodby\Interpreter
 *
 * PHP version 7
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Adapter\Goodby;

use Goodby\CSV\Import\Protocol\InterpreterInterface;
use Goodby\CSV\Import\Standard\Exception\StrictViolationException;

/**
 * Custom interpreter implementation which allows to reset observers and row consistency on every import.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   https://opensource.org/licenses/MIT
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class Interpreter implements InterpreterInterface
{

    /**
     * The array with the observers.
     *
     * @var array
     */
    private $observers = array();

    /**
     * The number of rows found in the header.
     *
     * @var int
     */
    private $rowConsistency = null;

    /**
     * Query whether or not strict mode is activated or not.
     *
     * @var boolean
     */
    private $strict = true;

    /**
     * Interpret the passed line.
     *
     * @param array $row The row that has to be processed
     *
     * @return void
     * @throws \Goodby\CSV\Import\Protocol\Exception\InvalidLexicalException Is thrown, if the passed line is not an array
     */
    public function interpret($row)
    {

        // check the row consistency
        $this->checkRowConsistency($row);

        // invoke the observers
        $this->notify($row);
    }

    /**
     * Disable strict mode.
     *
     * @return void
     */
    public function unstrict()
    {
        $this->strict = false;
    }

    /**
     * Add's the passed observer to the interpreter.
     *
     * @param callable $observer The observer to add
     *
     * @return void
     */
    public function addObserver(callable $observer)
    {
        $this->observers[] = $observer;
    }

    /**
     * Reset the interpreter.
     *
     * @return void
     */
    public function reset()
    {
        $this->observers = array();
        $this->rowConsistency = null;
    }

    /**
     * Query whether or not strict mode has been activated.
     *
     * @return boolean TRUE if the strict mode has NOT been activated, else FALSE
     */
    private function isNotStrict()
    {
        return $this->strict === false;
    }

    /**
     * Notify the observers.
     *
     * @param array $row The row that has to be processed
     *
     * @return void
     */
    private function notify(array $row)
    {

        // make the observers local
        $observers = $this->observers;

        // invoke the observers on the passed line
        foreach ($observers as $observer) {
            $this->delegate($observer, $row);
        }
    }

    /**
     * Delegate the row to the observer.
     *
     * @param callable $observer The observer that has to be invoked
     * @param array    $row      The row that has to be processed
     *
     * @return void
     */
    private function delegate(callable $observer, array $row)
    {
        call_user_func($observer, $row);
    }

    /**
     * Check if the column count is consistent with comparing other rows.
     *
     * @param array $row The row that has to be processed
     *
     * @return void
     * @throws \Goodby\CSV\Export\Standard\Exception\StrictViolationException Is thrown, if row consistency check fails
     */
    private function checkRowConsistency(array $row)
    {

        // query whether or not strict mode is enabled
        if ($this->isNotStrict()) {
            return;
        }

        // count the number of columns
        $current = count($row);

        // if the row consistency has not been set, set it
        if ($this->rowConsistency === null) {
            $this->rowConsistency = $current;
        }

        // check row consistency
        if ($current !== $this->rowConsistency) {
            throw new StrictViolationException(sprintf('Column size should be %u, but %u columns given', $this->rowConsistency, $current));
        }

        // set the new row consistency
        $this->rowConsistency = $current;
    }
}
