<?php

namespace AZ\Helpers\Renderer;


use Exception;

/**
 * Renderer
 */
abstract class Renderer
{


    /**
     * 
     *
     * @var string
     */
    protected string $file;

    /**
     * Render a View.
     * 
     * @param array $data List of variables as associative array
     * @return string
     */
    abstract public function render(array $data = []);    


    /**
     *
     * @param string $file
     * @throws Exception
     */
    public function __construct(string $file)
    {

        if (!file_exists($file)) {

            throw new Exception("File not found: '{$file}'", 1);
        }

        $this->file = $file;
    }


}