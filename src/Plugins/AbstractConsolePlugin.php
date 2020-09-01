<?php

/**
 * TechDivision\Import\Plugins\AbstractConsolePlugin
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 7
 *
 * @author    Marcus Döllerer <m.doellerer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Plugins;

use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TechDivision\Import\ApplicationInterface;
use TechDivision\Import\Cli\Application;

/**
 * Abstract console plugin implementation containing access to console commands and helpers.
 *
 * @author    Marcus Döllerer <m.doellerer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
abstract class AbstractConsolePlugin extends AbstractPlugin
{

    /**
     * The M2IF console application instance.
     *
     * @var Application
     */
    protected $cliApplication;

    /**
     * The console input instance.
     *
     * @var InputInterface
     */
    protected $input;

    /**
     * The console output instance.
     *
     * @var OutputInterface
     */
    protected $output;

    /**
     * The helper set.
     *
     * @var HelperSet
     */
    protected $helperSet;

    /**
     * AbstractConsolePlugin constructor
     *
     * @param ApplicationInterface $application
     *
     * @throws \Exception
     */
    public function __construct(ApplicationInterface $application)
    {

        // inject the cli application
        $cliApplication = $application->getContainer()->get('application');
        if (!$cliApplication instanceof Application) {
            throw new \Exception('No console application configured, please check your configuration.');
        }
        $this->setCliApplication($cliApplication);

        // set the console input
        $input = $application->getContainer()->get('input');
        if (!$input instanceof InputInterface) {
            throw new \Exception('No console input configured, please check your configuration.');
        }
        $this->setInput($input);

        // set the console output
        $output = $application->getContainer()->get('output');
        if (!$output instanceof OutputInterface) {
            throw new \Exception('No console output configured, please check your configuration.');
        }
        $this->setOutput($output);

        // inject the helper set
        $helperSet = $this->getCliApplication()->getHelperSet();
        if (!$helperSet instanceof HelperSet) {
            throw new LogicException('No HelperSet is defined.');
        }
        $this->setHelperSet($helperSet);

        parent::__construct($application);
    }

    /**
     * Returns the import cli application instance.
     *
     * @return \TechDivision\Import\Cli\Application
     */
    public function getCliApplication()
    {
        return $this->cliApplication;
    }

    /**
     * @param \TechDivision\Import\Cli\Application $cliApplication
     *
     * @return void
     */
    public function setCliApplication(Application $cliApplication)
    {
        $this->cliApplication = $cliApplication;
    }

    /**
     * Returns the console input instance.
     *
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return void
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Returns the console output instance.
     *
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Returns the helper set.
     *
     * @return \Symfony\Component\Console\Helper\HelperSet
     */
    public function getHelperSet()
    {
        return $this->helperSet;
    }

    /**
     * @param HelperSet $helperSet
     *
     * @return void
     */
    public function setHelperSet(HelperSet $helperSet)
    {
        $this->helperSet = $helperSet;
    }

    /**
     * Retrieve a helper by name.
     *
     * @param string $name The name of the helper to retrieve
     *
     * @return HelperInterface
     */
    public function getHelper($name)
    {
        return $this->getHelperSet()->get($name);
    }
}
