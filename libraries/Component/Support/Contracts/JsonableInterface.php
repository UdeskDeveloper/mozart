<?php namespace Mozart\Component\Support\Contracts;

interface JsonableInterface
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int    $options
     * @return string
     */
    public function toJson($options = 0);

}
