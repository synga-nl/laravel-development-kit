<?php

namespace Synga\LaravelDevelopment\Files;

/**
 * Class ComposerFile
 * @package Synga\LaravelDevelopment\Files
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
     * @param string $command
     * @param string $event
     * @param string|null $after
     * @return bool
     */
    public function addCommand(string $command, string $event, string $after = null)
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

    /**
     * @param string $package
     * @return bool
     */
    public function hasPackageInFile(string $package)
    {
        $packages = array_merge($this->get('require', []), $this->get('require_dev', []));

        return isset($packages[$package]);
    }

    /**
     * @param bool $production
     * @param bool $devevlopment
     * @return array
     */
    public function getPackages(bool $production = true, bool $devevlopment = false)
    {
        $packages = array_merge(
            (true === $production) ? $this->get('require') : [],
            (true === $devevlopment) ? $this->get('require-dev') : []
        );

        $result = [];

        foreach ($packages as $packageName => $packageVersion) {
            if (false !== strpos($packageName, '/')) {
                $result[] = [
                    'name' => $packageName,
                    'version' => $packageVersion
                ];
            }
        }

        return $result;
    }
}