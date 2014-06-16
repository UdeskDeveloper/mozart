<?php

namespace Mozart\Bundle\BackofficeBundle\Redux;

/**
 * Class SectionManager
 *
 * @package Mozart\Bundle\BackofficeBundle\Redux
 */
class SectionManager
{
    /**
     * @var array
     */
    private $sections;

    /**
     *
     */
    public function __construct()
    {
        $this->sections = array();
    }

    /**
     * @param $section
     * @param $alias
     */
    public function addSection( $section, $alias )
    {
        $this->sections[$alias] = $section;
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param $alias
     *
     * @return mixed
     */
    public function getSection( $alias )
    {
        if (array_key_exists( $alias, $this->sections )) {
            return $this->sections[$alias];
        }
    }
}
