<?php

namespace Mozart\Bundle\PostBundle\Model;

use Mozart\Bundle\PostBundle\Model\Post;

/**
 * Interface AttachmentManagerInterface
 *
 * @package Mozart\Bundle\PostBundle\Model
 */
interface AttachmentManagerInterface
{
    /**
     * @param Post $post
     *
     * @return AttachmentInterface[]
     */
    public function findAttachmentsByPost( Post $post );

    /**
     * @param $id integer
     *
     * @return AttachmentInterface[]
     */
    public function findOneAttachmentById( $id );

    /**
     * @param Post $post
     *
     * @return mixed
     */
    public function findFeaturedImageByPost( Post $post );
}
