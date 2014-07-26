<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Component\Form\Field;


use Mozart\Component\Form\Field;

class Table extends Field
{
    public function render()
    {

        ?>

        <table class="widefat table table-striped">
            <thead>
            <tr>
                <?php foreach ($this->field['columns'] as $column) {
                    ?>
                    <th class="nobg"><?php echo $column['title']; ?>
                        <div class="tooltip" title="<?php echo $column['desc']; ?>"></div>
                    </th>
                <?php
                } ?>

            </tr>
            </thead>
            <tfoot>
            <tr>
                <?php foreach ($this->field['columns'] as $column) {
                    ?>
                    <th class="nobg"><?php echo $column['title']; ?> </th>
                <?php
                } ?>
            </tr>
            </tfoot>
            <tbody>
            <?php

            foreach ($options['taxonomies'] as $taxonomy => $value) {

                // If the taxonomy is active, set the 'checked' class
                if (!empty( $value['enabled'] )) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                    $options['taxonomies'][$taxonomy]['enabled'] = 0;
                }

                if (empty( $value["autocomplete"] )) {
                    $options["taxonomies"][$taxonomy]["autocomplete"] = 0;
                }

// Generate the list of terms for the "Count" tooltip
                $terms = get_terms( $taxonomy );
                $termcount = count( $terms );
                $termstring = '';
                foreach ($terms as $term) {
                    $termstring .= $term->name . ', ';
                }
                ?>
                <tr>
                    <th scope="row" class="tax"><span id="<?php echo $taxonomy . '-title' ?>"
                                                      class="<?php echo $checked ?>"><?php echo $taxonomy ?>:<div
                                class="VS-icon-cancel"></div></span>
                    </th>
                    <td>
                        <input class="checkbox" type="checkbox" id="<?php echo $taxonomy ?>"
                               name="wpus_options[taxonomies][<?php echo $taxonomy ?>][enabled]"
                               value="1" <?php echo checked(
                            $options['taxonomies'][$taxonomy]['enabled'],
                            1,
                            false
                        ) ?> />
                    </td>
                    <td>
                        <input class="" type="text" id="<?php echo $taxonomy ?>"
                               name="wpus_options[taxonomies][<?php echo $taxonomy ?>][label]" size="20"
                               placeholder="<?php echo $taxonomy ?>"
                               value="<?php echo esc_attr( $options['taxonomies'][$taxonomy]['label'] ) ?>"/>
                    </td>
                    <td><?php echo $termcount ?>
                        <div class="tooltip" title="<?php echo $termstring ?>"></div>
                    </td>
                    <td>
                        <input class="" type="text" id="<?php echo $taxonomy ?>"
                               name="wpus_options[taxonomies][<?php echo $taxonomy ?>][max]" size="3" placeholder="0"
                               value="<?php echo esc_attr( $options['taxonomies'][$taxonomy]['max'] ) ?>"/>
                    </td>
                    <td>
                        <input class="" type="text" id="<?php echo $taxonomy ?>"
                               name="wpus_options[taxonomies][<?php echo $taxonomy ?>][exclude]" size="30"
                               placeholder=""
                               value="<?php echo esc_attr( $options['taxonomies'][$taxonomy]['exclude'] ) ?>"/>
                    </td>
                    <td>
                        <input class="" type="text" id="<?php echo $taxonomy ?>"
                               name="wpus_options[taxonomies][<?php echo $taxonomy ?>][include]" size="30"
                               placeholder=""
                               value="<?php echo( isset( $options['taxonomies'][$taxonomy]['include'] ) ? esc_attr(
                                   $options['taxonomies'][$taxonomy]['include']
                               ) : '' ) ?>"/>
                    </td>
                    <td>
                        <input class="checkbox" type="checkbox"
                               name="wpus_options[taxonomies][<?php echo $taxonomy ?>][autocomplete]"
                               value="1" <?php echo checked(
                            $options["taxonomies"][$taxonomy]["autocomplete"],
                            1,
                            false
                        ) ?> />
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php
    }
} 