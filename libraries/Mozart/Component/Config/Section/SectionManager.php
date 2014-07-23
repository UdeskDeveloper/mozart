<?php

namespace Mozart\Component\Config\Section;

/**
 * Class SectionManager
 *
 * @package Mozart\Component\Config\Section
 */
class SectionManager
{
    /**
     * @var array
     */
    private $sections;

    public function __construct()
    {
        $this->sections = array();
    }

    /**
     * @param ConfigSectionInterface $section
     * @param null $alias
     */
    public function addSection( ConfigSectionInterface $section, $alias = null )
    {
        if (null === $alias) {
            $alias = $section->getAlias();
        }

        $this->sections[$alias] = $section->getConfiguration();
    }

    public function updateSection($alias, $data = array()) {
        foreach ($data as $dataKey => $dataValue) {
            if (is_array($dataValue)) {
                array_merge($this->sections[$alias][$dataKey], $dataValue);
            }
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
    public function getSection( $alias )
    {
        if (array_key_exists( $alias, $this->sections )) {
            return $this->sections[$alias];
        }
    }

    public function addSubmenuPages($pageSlug, $pagePermissions)
    {
        foreach ($this->getSections() as $k => $section) {
            if ($section['type'] === 'divide') {
                continue;
            }
            $canBeSubSection =  !$section['type'] ? true : false;

            if (!isset( $section['title'] ) ||
                ( $canBeSubSection &&
                    ( isset( $section['subsection'] ) &&
                        $section['subsection'] == true ) )
            ) {
                continue;
            }

            if (isset( $section['submenu'] ) && $section['submenu'] == false) {
                continue;
            }

            if (isset( $section['customizer_only'] ) && $section['customizer_only'] == true) {
                continue;
            }

            add_submenu_page(
                $pageSlug,
                $section['title'],
                $section['title'],
                $pagePermissions,
                $pageSlug . '&tab=' . $k,
                '__return_null'
            );
        }
    }
}
