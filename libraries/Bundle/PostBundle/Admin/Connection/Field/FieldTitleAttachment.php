<?php
namespace Mozart\Bundle\PostBundle\Admin\Connection\Field;

class FieldTitleAttachment extends FieldTitle
{
    public function get_data($item)
    {
        $data = array(
            'title-attr' => $item->get_object()->post_title,
        );

        return $data;
    }
}
