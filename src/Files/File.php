<?php
namespace Synga\LaravelDevelopment\Files;

/**
 * Class File
 * @package Synga\LaravelDevelopment\Files
 */
abstract class File
{
    /**
     * The path to the composer file.
     *
     * @var string
     */
    private $path;

    /**
     * The data in the file
     *
     * @var array
     */
    protected $data;

    /**
     * ComposerFile constructor.
     * @param $path
     */
    public function __construct($path)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException('The given path "' . $path . '" is not valid');
        }

        $this->path = $path;
    }

    /**
     * Reads the JSON file
     *
     * @return mixed
     */
    public function &read()
    {
        if (empty($this->data)) {
            $this->data = json_decode(file_get_contents($this->path), true);
        }

        return $this->data;
    }

    /**
     * Writes the modified file to the given path
     *
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
     * Gets a value from the data of the current object
     *
     * @param $key
     * @param $default
     * @return mixed
     */
    public function get($key, $default = [])
    {
        return array_get($this->read(), $key, $default);
    }

    /**
     * Sets a value in the data of the current object
     *
     * @param $key
     * @param $data
     * @return array
     */
    public function set($key, $data)
    {
        return array_set($this->read(), $key, $data);
    }

    /**
     * Sets the given data as data of the object, if it needs to be written to the file, call the method write.
     *
     * @param $data
     */
    public function setFileContents($data)
    {
        $this->data = $data;
    }
}