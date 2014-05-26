<div class="form-group">
    <?php global $post; ?>
    <label>
        <?php echo __('Price', 'mozart'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('price'); ?>
        <div class="input-group">
            <input type="number" class="form-control"
                   name="<?php $mb->the_name(); ?>" value="<?php echo get_post_meta($post->ID, '_property_price', true) ?>">
            <span class="input-group-addon"><?php print parameter('money', 'currency', 'sign'); ?></span>
        </div>
    </div>
</div>

<div class="form-group">
    <label>
        <?php echo __('Bathrooms', 'mozart'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('bathrooms'); ?>
        <input type="number" class="form-control ui-slider"
               data-slider-id="submission-bathrooms-slider-control"
               data-slider-value="<?php echo get_post_meta($post->ID, '_property_bathrooms', true) ?>"
               data-slider-tooltip="always"
               name="<?php $mb->the_name(); ?>"
               value="<?php echo get_post_meta($post->ID, '_property_bathrooms', true) ?>">
    </div>
</div>

<div class="form-group">
    <label>
        <?php echo __('Bedrooms', 'mozart'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('bedrooms'); ?>
        <input type="number" class="form-control ui-slider"
               data-slider-id="submission-bedrooms-slider-control"
               data-slider-value="<?php echo get_post_meta($post->ID, '_property_bedrooms', true) ?>"
               data-slider-tooltip="always"
               name="<?php $mb->the_name(); ?>" value="<?php echo get_post_meta($post->ID, '_property_bedrooms', true) ?>">
    </div>
</div>

<div class="form-group">
    <label>
        <?php echo __('Area', 'mozart'); ?>
    </label>

    <div class="controls">
        <?php $mb->the_field('area'); ?>
        <div class="input-group">
            <input type="number" class="form-control"
                   name="<?php $mb->the_name(); ?>" value="<?php echo get_post_meta($post->ID, '_property_area', true) ?>">
            <span class="input-group-addon"><?php print parameter('property', 'units', 'area'); ?></span>
        </div>
    </div>
</div>

<div class="form-group">
    <label>
        <?php echo __('GPS', 'mozart'); ?>
    </label>
</div>

<div class="form-group">
    <?php $mb->the_field('location_search'); ?>
    <?php $mb->the_value(); ?>

    <label>
        <?php echo __('Location search', 'mozart'); ?>
    </label>
    <input id="location-selector" class="form-control location"
           type="text" name="<?php $mb->the_name(); ?>"
           value="<?php echo get_post_meta($post->ID, '_property_location_search', true) ?>"
           placeholder="<?php echo __('Search location', 'mozart'); ?>"/>
</div>
<div class="form-group">
    <?php $mb->the_field('latitude'); ?>
    <?php $value = get_post_meta($post->ID, '_property_latitude', true) ?>
    <?php $value = !empty($value) ? $value : parameter('property', 'map', 'latitude'); ?>
    <label><?php echo __('Latitude', 'mozart'); ?></label>
    <input id="latitude-selector" class="form-control latitude" type="text"
           name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>"
           placeholder="<?php echo __('Latitude', 'mozart'); ?>"/>

</div>
<div class="form-group">
    <?php $mb->the_field('longitude'); ?>
    <?php $value = get_post_meta($post->ID, '_property_longitude', true) ?>
    <?php $value = !empty($value) ? $value : parameter('property', 'map', 'longitude'); ?>
    <label><?php echo __('Longitude', 'mozart'); ?></label>
    <input id="latitude-selector" class="form-control longitude" type="text"
           name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>"
           placeholder="<?php echo __('Longitude', 'mozart'); ?>"/>

    <div id="map" style="height:300px; width: 100%;" class="mtl">

    </div>
</div>