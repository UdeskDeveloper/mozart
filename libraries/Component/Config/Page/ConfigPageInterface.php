<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Config\Page;

interface ConfigPageInterface
{
    public function getKey();

    public function getName();

    public function getParent();

    public function getUserCapabilities();

    public function getMenuPosition();

    public function getIconUrl();

    public function toRedirect();

    public function getShortName();
}
