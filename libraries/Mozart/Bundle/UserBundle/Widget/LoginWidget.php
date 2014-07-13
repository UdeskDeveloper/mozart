<?php

namespace Mozart\Bundle\UserBundle\Widget;

use Mozart\Bundle\WidgetBundle\Widget;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class LoginWidget
 *
 * @package Mozart\Bundle\UserBundle\Widget
 */
class LoginWidget extends Widget
{
    /**
     * @var \Symfony\Component\Templating\EngineInterface
     */
    private $templating;

    /**
     * @param EngineInterface $templating
     */
    public function __construct( EngineInterface $templating )
    {
        $this->templating = $templating;
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getFieldGroup()
    {
        return array(
            'key'                   => 'group_53c2f0d129f16',
            'title'                 => 'Login Widget Title',
            'fields'                => array(
                array(
                    'key'               => 'field_53c2f0d57a7d2',
                    'label'             => 'Title',
                    'name'              => 'mozart_user_login_title',
                    'prefix'            => '',
                    'type'              => 'text',
                    'instructions'      => '',
                    'required'          => 0,
                    'conditional_logic' => 0,
                    'default_value'     => 'Login',
                    'placeholder'       => 'Login',
                    'prepend'           => '',
                    'append'            => '',
                    'maxlength'         => '',
                    'readonly'          => 0,
                    'disabled'          => 0,
                ),
            ),
            'location'              => array(
                array(
                    array(
                        'param'    => 'widget',
                        'operator' => '==',
                        'value'    => 'login_widget_mozart',
                    ),
                ),
            ),
            'menu_order'            => 0,
            'position'              => 'normal',
            'style'                 => 'default',
            'label_placement'       => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen'        => '',
        );
    }


    /**
     * @param array $args
     * @param array $instance
     */
    public function widget( $args, $instance )
    {
        echo $this->templating->render(
            'MozartUserBundle:Account:widgets/login.html.twig',
            array(
                'title'         => apply_filters( 'widget_title', $instance['mozart_user_login_title'] ),
                'before_widget' => $args['before_widget'],
                'after_widget'  => $args['after_widget'],
                'before_title'  => $args['before_title'],
                'after_title'   => $args['after_title']
            )
        );
    }

}
