<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Form\Field;


use Mozart\Component\Form\Field;

class Heading extends Field {

    public function render() {
        echo '<div id="section-' . $this->field['id'] . '" class="redux-section-field redux-field">';

        echo '<h3>' . $this->field['title'] . '</h3>';

        echo '</div>';
    }
} 