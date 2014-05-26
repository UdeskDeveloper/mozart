<?php

?>

<table class="mozart-options">
    <?php do_action('before_agent_options_fields', $mb); ?>

    <tr>
        <th>
            <label><?php print __( 'Mobile', 'mozart' ); ?></label>
        </th>
        <td>
            <?php $mb->the_field( 'mobile' ); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php print __( 'Phone', 'mozart' ); ?></label>
        </th>
        <td>
            <?php $mb->the_field( 'phone' ); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php print __( 'E-mail', 'mozart' ); ?></label>
        </th>
        <td>
            <?php $mb->the_field( 'email' ); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>
</table>
