<?php
namespace Mozart\Bundle\PostBundle\Admin\Connection\Field;

class FieldTitleUser extends FieldTitle
{
    public function get_data($user)
    {
        return array(
            'title-attr' => '',
        );
    }
}
