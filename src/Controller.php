<?php

namespace App;

/**
 * Class Controller
 *
 * The base controller for all controllers that this application uses.
 *
 * @package App
 */
class Controller
{

    /** @var array Data to be sent to the view. */
    private $data = [];

    /**
     * Set a variable which can then be used in the view.
     *
     * @param mixed $name The name of the variable to set.
     * @param mixed $value The value of the variable to set.
     */
    protected function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Get the variables that have been set.
     *
     * @return array An array of variables that have been set.
     */
    public function getData()
    {
        return $this->data;
    }

}
