<?php

namespace Synga\LaravelDevelopment;

/**
 * Class ComposerFile
 * @package Synga\LaravelDevelopment
 */
class ComposerFile
{
    /**
     * The path to the composer file.
     *
     * @var string
     */
    private $path;

    /**
     * Configuration of composer for the given file.
     *
     * @var array
     */
    private $configuration;

    /**
     * All possible events composer triggers.
     *
     * @var array
     */
    protected $composerEvents = [
        'pre-install-cmd',
        'post-install-cmd',
        'pre-update-cmd',
        'post-update-cmd',
        'post-status-cmd',
        'pre-archive-cmd',
        'post-archive-cmd',
        'pre-autoload-dump',
        'post-autoload-dump',
        'post-root-package-install',
        'post-create-project-cmd',
        'pre-dependencies-solving',
        'post-dependencies-solving',
        'pre-package-install',
        'post-package-install',
        'pre-package-update',
        'post-package-update',
        'pre-package-uninstall',
        'post-package-uninstall',
        'init',
        'command',
        'pre-file-download',
    ];

    /**
     * @var array
     */
    protected $selectors = [
        'commands' => 'scripts'
    ];

    /**
     * ComposerFile constructor.
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function &read()
    {
        if (empty($this->configuration)) {
            $this->configuration = json_decode(file_get_contents($this->path), true);
        }

        return $this->configuration;
    }

    /**
     * @param $configuration
     */
    public function set($configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return bool|int
     */
    public function write()
    {
        $configuration = $this->read();
        if (empty($configuration['require'])) {
            unset($configuration['require']);
        }

        return file_put_contents(
            $this->path,
            json_encode(
                $configuration,
                JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            )
        );
    }

    /**
     * @param $command
     * @param $event
     * @param null $after
     * @return bool|mixed
     */
    public function addCommand($command, $event, $after = null)
    {
        $added = false;

        if (in_array($event, $this->composerEvents)) {
            $key = $this->selectors['commands'] . '.' . $event;

            $scripts = array_get($this->read(), $key);
            if (empty($scripts) || in_array($command, $scripts)) {
                return;
            }

            if (!is_array($scripts)) {
                $scripts = [];
            }

            if (null !== $after) {
                $tempScripts = [];

                foreach ($scripts as $script) {
                    $tempScripts[] = $script;

                    if ($after === $script) {
                        $tempScripts[] = $command;
                        $added = true;
                    }
                }

                $scripts = $tempScripts;
            }

            if (false === $added) {
                $scripts[] = $command;
            }

            array_set($this->read(), $key, $scripts);
        }

        return true;
    }
}