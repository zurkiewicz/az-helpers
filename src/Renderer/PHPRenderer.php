<?php

namespace AZ\Helpers\Renderer;


class PHPRenderer extends Renderer
{


    /**
     * Render a View.
     * 
     * @param array $data List of variables as associative array
     * @return string
     */
    public function render(array $data = [])
    {

        extract($data, EXTR_SKIP);

        ob_start();
        include $this->file;
        return ob_get_clean();
    }
}