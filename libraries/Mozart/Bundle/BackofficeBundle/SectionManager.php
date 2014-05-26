<?php

namespace Mozart\Bundle\BackofficeBundle;

class SectionManager
{
    private $sections;

    public function __construct()
    {
        $this->sections = array();
    }

    public function addSection($section, $alias)
    {
        $this->sections[$alias] = $section;
    }

    public function getSections()
    {
        return $this->sections;
    }

    public function getSection($alias)
    {
        if (array_key_exists($alias, $this->sections)) {
           return $this->sections[$alias];
        }
    }
}
