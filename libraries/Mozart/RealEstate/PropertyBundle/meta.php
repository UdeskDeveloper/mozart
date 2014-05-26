<style>
    #wpa_loop-slides .wpa_group { border:1px solid #ccc; padding:10px; background-color: #e3e3e3; margin-bottom: 5px; cursor: move; }
</style>

<script type="text/javascript">
    (function ($) {
        $(function () {
            $("#wpa_loop-slides").sortable({
                change: function () {
                    $("#warning").show();
                }
            });
        });
    }(jQuery));
</script>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $('.obtain-gps').on({
            click: function (e) {
                e.preventDefault();
                var title = $('input#title').val();
                var locations = $('#locationschecklist');

                if (title == '') {
                    alert('<?php echo __('Please fill the address (Title field).', 'mozart'); ?>');

                    return;
                }

                var checked_locations = $('input[type=checkbox]:checked', locations);

                if (checked_locations.length === 0) {
                    alert('<?php echo __('Please check the location (Locations checkboxes).', 'mozart'); ?>');

                    return;
                }

                if (checked_locations.length > 1) {
                    alert('<?php echo __('Please check just one location (Locations checkbox).', 'mozart'); ?>');

                    return;
                }

                var location = $('input[type=checkbox]:checked', locations).parent().text();
                var geocoder = new google.maps.Geocoder();

                geocoder.geocode({'address': title + ', ' + location}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        $('.latitude').attr('value', results[0].geometry.location.lat());
                        $('.longitude').attr('value', results[0].geometry.location.lng());
                    } else {
                        alert("<?php echo __('Geocode was not successful for the following reason: ', 'mozart'); ?>" + status);
                    }
                });
            }
        })
    });
</script>

<table class="mozart-options">
    <?php do_action('before_property_options_fields', $mb); ?>
    <tr>
        <th>
            <label><?php echo __('ID', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('id'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />

            <p class="description">
                <?php echo __('Leave empty for default ID.', 'mozart'); ?>
            </p>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Optional Title', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('title'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />

            <p class="description">
                <?php echo __('It will be used in widgets and properties grid & rows layout.', 'mozart'); ?>
            </p>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Custom text instead of price', 'mozart'); ?></label>
        </th>

        <td>
            <?php $mb->the_field('custom_text'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Price', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('price'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Price suffix', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('price_suffix'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Bathrooms', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('bathrooms'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Hide baths', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('hide_baths'); ?>
            <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1" <?php checked($mb->get_the_value()); ?>/>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Bedrooms', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('bedrooms'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Hide beds', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('hide_beds'); ?>
            <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1" <?php checked($mb->get_the_value()); ?>/>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Area', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('area'); ?>
            <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" />
        </td>
    </tr>

    <tr>

        <th>
            <?php echo __('GPS', 'mozart'); ?>
        </th>
        <td>
            <?php $mb->the_field('location_search'); ?>
            <?php $mb->the_value(); ?>
            <label>
                <?php echo __('Location search', 'mozart'); ?>
            </label>
            <input id="location-selector" class="location" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" placeholder="<?php echo __('Search location', 'mozart'); ?>"/>

            <?php $mb->the_field('latitude'); ?>
            <?php $value = $mb->get_the_value(); ?>
            <?php $value = !empty($value) ? $value : parameter('property', 'map', 'latitude'); ?>
            <label><?php echo __('Latitude', 'mozart'); ?></label>
            <input id="latitude-selector" class="latitude" type="text" name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>" placeholder="<?php echo __('Latitude', 'mozart'); ?>"/>

            <?php $mb->the_field('longitude'); ?>
            <?php $value = $mb->get_the_value(); ?>
            <?php $value = !empty($value) ? $value : parameter('property', 'map', 'longitude'); ?>
            <label><?php echo __('Longitude', 'mozart'); ?></label>
            <input id="latitude-selector" class="longitude" type="text" name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>" placeholder="<?php echo __('Longitude', 'mozart'); ?>"/>
            <br /><br />
            <div id="map" style="height:300px; width: 100%;">

            </div>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Featured', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('featured'); ?>
            <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1" <?php checked($mb->get_the_value()); ?>/>
        </td>
    </tr>

    <tr>
        <th>
            <label><?php echo __('Reduced', 'mozart'); ?></label>
        </th>
        <td>
            <?php $mb->the_field('reduced'); ?>
            <input type="checkbox" name="<?php $mb->the_name(); ?>" value="1" <?php checked($mb->get_the_value()); ?>/>
        </td>
    </tr>

    <tr>
        <th><label><?php echo __('Slider image', 'mozart'); ?></label></th>
        <td>
            <?php $property_slider_media_metabox = new \WPAlchemy_MediaAccess(); ?>

            <?php $mb->the_field('slider_image'); ?>
            <?php echo $property_slider_media_metabox->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
            <?php echo $property_slider_media_metabox->getButton(); ?>
        </td>
    </tr>

    <tr>
        <th><label><?php echo __('Images', 'mozart'); ?></label></th>
        <td>
            <?php $property_media_metabox = new \WPAlchemy_MediaAccess(); ?>

            <?php while ($mb->have_fields_and_multi('slides')): ?>
                <?php $mb->the_group_open(); ?>

                <?php $mb->the_field('imgurl'); ?>
                <?php $property_media_metabox->setGroupName('img-n' . $mb->get_the_index())->setInsertButtonLabel(__('Insert', 'mozart')); ?>

                <p>
                    <?php echo $property_media_metabox->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value())); ?>
                    <?php echo $property_media_metabox->getButton(); ?>
                    <a href="#" class="dodelete button"><?php echo __('Remove', 'mozart'); ?></a>
                </p>

                <?php $mb->the_group_close(); ?>
            <?php endwhile; ?>

            <p>
                <a href="#" class="docopy-slides docopy-docs button button-primary"><?php echo __('Add', 'mozart'); ?></a>
            </p>
        </td>
    </tr>

    <?php do_action('after_property_options_fields', $mb); ?>
</table>
