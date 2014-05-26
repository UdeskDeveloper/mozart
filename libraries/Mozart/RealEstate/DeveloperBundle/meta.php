<table class="mozart-options">
    <tr>
        <th>
            <label><?php print __('Address', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('address'); ?>
            <textarea type="text" name="<?php $mb->the_name(); ?>" cols="80" rows="8"><?php $mb->the_value(); ?></textarea>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php print __('Phone', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('phone'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php print __('E-mail', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('email'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php print __('URL', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('url'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>
</table>
