<?php

namespace Mozart\Bundle\OptionBundle\Redux;

/**
 * Class SectionManager
 *
 * @package Mozart\Bundle\OptionBundle\Redux
 */
/**
 * Class SectionManager
 *
 * @package Mozart\Bundle\OptionBundle\Redux
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
     * @param ReduxSection $section
     * @param null         $alias
     */
    public function addSection(ReduxSection $section, $alias = null)
    {
        if (null === $alias) {
            $this->sections[] = $section->getConfiguration();
        } else {
            $this->sections[$alias] = $section->getConfiguration();
        }
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
    public function getSection($alias)
    {
        if (array_key_exists( $alias, $this->sections )) {
            return $this->sections[$alias];
        }
    }
}
