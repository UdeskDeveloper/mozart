<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bridge\WordPress\Translation;

use Symfony\Bundle\FrameworkBundle\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator
{
    public function getLocale()
    {
        if (null === $this->locale) {
            $this->locale = get_locale();
        }

        return $this->locale;
    }

}
