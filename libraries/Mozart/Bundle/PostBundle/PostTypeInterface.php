<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\PostBundle;

/**
 * Interface PostTypeInterface
 *
 * @package Mozart\Bundle\PostBundle
 */
interface PostTypeInterface
{

    /**
     * @return mixed
     */
    public function getKey();

    /**
     * @return mixed
     */
    public function getConfiguration();

}
