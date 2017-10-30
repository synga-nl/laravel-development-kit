<?php

namespace Synga\LaravelDevelopment\Console;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

/**
 * Class ApproveExecCommand
 * @package Synga\LaravelDevelopment\Console
 */
class ApproveExecCommand
{
    /**
     * @var InputInterface
     */
    private static $input;

    /**
     * @var OutputInterface
     */
    private static $output;

    /**
     * @var QuestionHelper
     */
    private static $questionHelper;

    /**
     * @var int
     */
    private static $retries = 3;

    /**
     * @var int
     */
    private static $currentRetries = 0;

    /**
     * This should be called when the event
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public static function setInputOutput(InputInterface $input, OutputInterface $output)
    {
        self::$input = $input;
        self::$output = $output;
        self::$questionHelper = new QuestionHelper();
    }

    /**
     * Executes a shell command if the user approves the command
     *
     * @param $command
     * @param $output
     * @param $returnVar
     */
    public static function exec($command, &$output = null, &$returnVar = null)
    {
        $answer = self::ask($command, 'y');

        if (true === $answer) {
            exec($command, $output, $returnVar);
        }
    }

    /**
     * Asks the user if the command can be executed
     *
     * @param $command
     * @param $defaultValue
     * @return bool
     */
    private static function ask($command, $defaultValue)
    {
        $confirmValues = ['y', 'true', 'yes'];
        $rejectValues = ['n', 'false', 'no'];

        while (true) {
            $answer = self::askQuestion(
                "Are you sure you want to execute the following command on the shell:\r\n" . $command . "\r\n[y,n]: ",
                $defaultValue
            );

            $confirmed = in_array($answer, $confirmValues);
            $inRejectValues = in_array($answer, $rejectValues);

            if (true === $inRejectValues) {
                return false;
            }

            if (false === $confirmed) {
                self::$currentRetries++;
            }

            if (self::$currentRetries === self::$retries) {
                self::$currentRetries = 0;

                return false;
            }

            if (true === $confirmed) {
                self::$currentRetries = 0;

                return true;
            }
        }
    }

    /**
     * Asks the user a question.
     *
     * @param $question
     * @param string $defaultValue
     * @return string
     */
    private static function askQuestion($question, $defaultValue = 'n')
    {
        return strtolower(self::$questionHelper->ask(
            self::$input,
            self::$output,
            new Question($question, $defaultValue)
        ));
    }
}