<?php

namespace Synga\LaravelDevelopment\Files;

/**
 * Class ComposerFile
 * @package Synga\LaravelDevelopment
 */
class ComposerFile extends File
{
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
     * @param $data
     */
    public function set($data)
    {
        $this->data = $data;
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
            if (is_array($scripts) && in_array($command, $scripts)) {
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