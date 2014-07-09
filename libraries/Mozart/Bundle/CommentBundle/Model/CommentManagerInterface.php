<?php

namespace Mozart\Bundle\CommentBundle\Model;

use Mozart\Bundle\PostBundle\Model\PostInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface CommentManagerInterface
 * @package Mozart\Bundle\CommentBundle\Model
 */
interface CommentManagerInterface
{
    /**
     * @param  PostInterface $post
     * @param  Request       $request
     * @return mixed
     */
    public function createComment(PostInterface $post, Request $request);

    /**
     * @param  CommentInterface $comment
     * @return mixed
     */
    public function deleteComment(CommentInterface $comment);

    /**
     * @param  CommentInterface $comment
     * @return mixed
     */
    public function updateComment(CommentInterface $comment);

    /**
     * @return mixed
     */
    public function getClass();

    /**
     * @param  PostInterface $post
     * @return mixed
     */
    public function findCommentsByPost(PostInterface $post);
}
