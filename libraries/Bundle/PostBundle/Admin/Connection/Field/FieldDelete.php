<?php
namespace Mozart\Bundle\PostBundle\Admin\Connection\Field;

class FieldDelete implements FieldInterface
{
    public function get_title()
    {
        $data = array(
            'title' => __( 'Delete all connections', P2P_TEXTDOMAIN )
        );

        return P2P_Mustache::render( 'column-delete-all', $data );
    }

    public function render($p2p_id, $_)
    {
        $data = array(
            'p2p_id' => $p2p_id,
            'title' => __( 'Delete connection', P2P_TEXTDOMAIN )
        );

        return P2P_Mustache::render( 'column-delete', $data );
    }
}
