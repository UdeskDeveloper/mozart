<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Plugin;

interface PluginInterface
{
    public function getSlug();

    public function getName();

    public function getBasename();

    public function isActive();

    public function getVersion();

    public function isRequired();

    public function getExternalUrl();

    public function getSource();

}
