<?php
namespace Synga\LaravelDevelopment\Files;

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
        $this->path = $path;
    }

    /**
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
}