<?php

namespace Mozart\Bundle\ShortcodeBundle;

interface ShortcodeInterface
{
    public function getName();

    public function process($content);
}
