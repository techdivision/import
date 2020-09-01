<?php

/**
 * TechDivision\Import\Plugins\DebugSendPlugin
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

use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use TechDivision\Import\Configuration\Jms\Configuration\SwiftMailer;

/**
 * Plugin that creates and sends a debug report via email.
 *
 * @author    Marcus Döllerer <m.doellerer@techdivision.com>
 * @copyright 2020 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import
 * @link      http://www.techdivision.com
 */
class DebugSendPlugin extends AbstractConsolePlugin
{

    /**
     * Process the plugin functionality.
     *
     * @return void
     */
    public function process()
    {

        // retrieve the SwiftMailer configuration
        $swiftMailerConfiguration = $this->getPluginConfiguration()->getSwiftMailer();

        // retrieve the question helper
        $questionHelper = $this->getHelper('question');

        // use the configured SwiftMail recipient address as default if possible
        if ($swiftMailerConfiguration instanceof SwiftMailer && $swiftMailerConfiguration->hasParam('to')) {
            $recipient = $swiftMailerConfiguration->getParam('to');
            // ask the user for the recipient address to send the debug report to
            $recipientQuestion = new Question(
                "<question>Please enter the email address of the debug report recipient (Configured: " . $recipient . "):\n</question>",
                $recipient
            );
        } else {
            $recipientQuestion = new Question(
                "<question>Please enter the email address of the debug report recipient:\n</question>"
            );
        }

        // ask the user to confirm the configured recipient address or enter a new one
        $recipient = $questionHelper->ask($this->getInput(), $this->getOutput(), $recipientQuestion);

        // warn the user about the impact of submitting their report and ask for confirmation
        $confirmationQuestion = new ConfirmationQuestion(
            "<comment>The debug report may contain confidential information (depending on the data you were importing).\n</comment>"
            . "<question>Do you really want to send the report to " . $recipient . "? (Y/n)\n</question>"
        );

        // abort the operation if the user does not confirm with 'y' or enter
        if (!$questionHelper->ask($this->getInput(), $this->getOutput(), $confirmationQuestion)) {
            $this->getOutput()->writeln('<info>Aborting operation - debug report has NOT been sent.</info>');
            return;
        }

        $this->getOutput()->writeln('<info>The debug report has successfully been submitted to ' . $recipient . '</info>');
    }
}
