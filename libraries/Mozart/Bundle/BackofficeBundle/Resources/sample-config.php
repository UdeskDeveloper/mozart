<?php
/**
  ReduxFramework Sample Config File
  For full documentation, please visit: https://github.com/ReduxFramework/ReduxFramework/wiki
 * */

if (!class_exists("Redux_Framework_sample_config")) {

    class Redux_Framework_sample_config
    {
    }

    new Redux_Framework_sample_config();
}

/**

  Custom function for the callback referenced above

 */
if (!function_exists('redux_my_custom_field')):

    public function redux_my_custom_field($field, $value)
    {
        print_r($field);
        print_r($value);
    }

endif;

/**

  Custom function for the callback validation referenced above

 * */
if (!function_exists('redux_validate_callback_function')):

    public function redux_validate_callback_function($field, $value, $existing_value)
    {
        $error = false;
        $value = 'just testing';
        /*
          do your validation

          if (something) {
          $value = $value;
          } elseif (something else) {
          $error = true;
          $value = $existing_value;
          $field['msg'] = 'your custom error message';
          }
         */

        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
        }

        return $return;
    }

endif;
