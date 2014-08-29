<?php
if (!class_exists('VC_Extensions_AnimateText')) {

    class VC_Extensions_AnimateText {
        function VC_Extensions_AnimateText() {
          wpb_map( array(
            "name" => __("Animate Text", 'vc_animatetext_cq'),
            "base" => "cq_vc_animatetext",
            "class" => "wpb_cq_vc_extension",
            "controls" => "full",
            "icon" => "icon-wpb-vc_extension_cq",
            "category" => __('Sike Extensions', 'js_composer'),
            'description' => __( 'Small label', 'js_composer' ),
            "params" => array(
              array(
                "type" => "textarea_html",
                "holder" => "",
                "class" => "vc_animatetext_cq",
                "heading" => __("Normal content", 'vc_animatetext_cq'),
                "param_name" => "content",
                "value" => __("I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.", 'vc_animatetext_cq'),
                "description" => __("", 'vc_animatetext_cq')
              ),
              array(
                "type" => "textfield",
                "holder" => "",
                "class" => "vc_animatetext_cq",
                "heading" => __("Animate text after the normal content", 'vc_animatetext_cq'),
                "param_name" => "text",
                "value" => __("animate", 'vc_animatetext_cq'),
                "description" => __("", 'vc_animatetext_cq')
              ),
               array(
                "type" => "textfield",
                "holder" => "",
                "class" => "vc_animatetext_cq",
                "heading" => __("Optional link for the animate text", 'vc_animatetext_cq'),
                "param_name" => "link",
                "value" => __("http://codecanyon.net/user/sike?ref=sike", 'vc_animatetext_cq'),
                "description" => __("", 'vc_animatetext_cq')
              ),
              array(
                "type" => "dropdown",
                "heading" => __("Custom link target", "vc_animatetext_cq"),
                "param_name" => "custom_links_target",
                "description" => __('Select how to open icon links.', 'vc_animatetext_cq'),
                'value' => array(__("Same window", "vc_animatetext_cq") => "_self", __("New window", "vc_animatetext_cq") => "_blank")
              ),
              array(
                "type" => "textfield",
                "holder" => "",
                "class" => "vc_animatetext_cq",
                "heading" => __("HTML tag for the animate text", 'vc_animatetext_cq'),
                "param_name" => "texttag",
                "value" => __("sup", 'vc_animatetext_cq'),
                "description" => __("Default is sup, you can use other tag like span, sub here too.", 'vc_animatetext_cq')
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Animate text background color", 'vc_extend'),
                "param_name" => "background",
                "value" => '#663399',
                "description" => __("", 'vc_extend')
              ),
              array(
                "type" => "colorpicker",
                "holder" => "div",
                "class" => "",
                "heading" => __("Animate text color", 'vc_extend'),
                "param_name" => "color",
                "value" => '#FFF',
                "description" => __("", 'vc_extend')
              ),
              array(
                "type" => "textfield",
                "holder" => "",
                "class" => "vc_animatetext_cq",
                "heading" => __("fontsize", 'vc_animatetext_cq'),
                "param_name" => "fontsize",
                "value" => __("11px", 'vc_animatetext_cq'),
                "description" => __("", 'vc_animatetext_cq')
              ),
              array(
                "type" => "textfield",
                "holder" => "",
                "class" => "vc_animatetext_cq",
                "heading" => __("margin", 'vc_animatetext_cq'),
                "param_name" => "margin",
                "value" => __("", 'vc_animatetext_cq'),
                "description" => __("", 'vc_animatetext_cq')
              ),
              // array(
              //   "type" => "textfield",
              //   "holder" => "",
              //   "class" => "vc_animatetext_cq",
              //   "heading" => __("padding", 'vc_animatetext_cq'),
              //   "param_name" => "padding",
              //   "value" => __("", 'vc_animatetext_cq'),
              //   "description" => __("", 'vc_animatetext_cq')
              // ),
              array(
                "type" => "dropdown",
                "holder" => "",
                "class" => "vc_animatetext_cq",
                "heading" => __("Animation style", "vc_animatetext_cq"),
                "param_name" => "animation",
                "value" => array(__("animation 1", "vc_animatetext_cq") => "animation-01", __("animation 2", "vc_animatetext_cq") => "animation-02", __("animation 3", "vc_animatetext_cq") => "animation-03", __("animation 4", "vc_animatetext_cq") => "animation-04", __("animation 5", "vc_animatetext_cq") => "animation-05", __("animation 6", "vc_animatetext_cq") => "animation-06", __("animation 7", "vc_animatetext_cq") => "animation-07", __("animation 8", "vc_animatetext_cq") => "animation-08", __("animation 9", "vc_animatetext_cq") => "animation-09"),
                "description" => __("", "vc_animatetext_cq")
              ),
              array(
                "type" => "checkbox",
                "holder" => "",
                "class" => "vc_animatetext_cq",
                "heading" => __("Add the animate text before the content text.", 'vc_animatetext_cq'),
                "param_name" => "tostart",
                "value" => array(__("Yes", "vc_animatetext_cq") => 'on'),
                "description" => __("Defautl the animate text will append to the end, check this if you want to put it in the start.", 'vc_animatetext_cq')
              )

            )
        ));

        function cq_vc_animatetext_func($atts, $content=null) {
          extract( shortcode_atts( array(
            'background' => '',
            'color' => '',
            'fontsize' => '',
            'margin' => '',
            'padding' => '',
            'text' => '',
            'link' => '',
            'custom_links_target' => '',
            'texttag' => 'sup',
            'tostart' => 'off',
            'animation' => ''
          ), $atts ) );


          wp_register_style( 'vc_animatetext_cq_style', plugins_url('css/style.css', __FILE__) );
          wp_enqueue_style( 'vc_animatetext_cq_style' );

          wp_register_script('vc_animatetext_cq_script', plugins_url('js/animatetext.min.js', __FILE__), array("jquery"));
          wp_enqueue_script('vc_animatetext_cq_script');

          $content = wpb_js_remove_wpautop($content); // fix unclosed/unwanted paragraph tags in $content
          $output = '';
          $splittext = '';
          if($tostart=="off") $output .= $content;

          // if($link!="") $splittext .= '<a href="'.$link.'" class="animat-text-link" target="'.$custom_links_target.'">';
          if($link!="") {
              $output .= '<a href="'.$link.'" class="animat-text-link" target="'.$custom_links_target.'">';
              if($animation=="animation-02"||$animation=="animation-03"){
                $text = str_split($text);
                // $splittext = '';
                for ($i=0; $i < count($text); $i++) {
                    $splittext .= '<span>';
                    $splittext .= $text[$i];
                    $splittext .= '</span>';
                }
              }else if($animation=="animation-08"||$animation=="animation-09"||$animation=="animation-04"){
                  $splittext .= '<span>';
                  $splittext .= $text;
                  $splittext .= '</span>';
              }else{
                  $splittext .= $text;
              }
              $output .= '<'.$texttag.' style="margin:'.$margin.';" class="'.$animation.' cq-animate-text" data-color="'.$color.'" data-background="'.$background.'" data-margin="'.$margin.'"  data-animation="'.$animation.'" data-fontsize="'.$fontsize.'">'.$splittext.'</'.$texttag.'>';
              $output .= '</a>';
          }else{
              if($animation=="animation-02"||$animation=="animation-03"){
                $text = str_split($text);
                // $splittext = '';
                for ($i=0; $i < count($text); $i++) {
                    $splittext .= '<span>';
                    $splittext .= $text[$i];
                    $splittext .= '</span>';
                }
              }else if($animation=="animation-08"||$animation=="animation-09"||$animation=="animation-04"){
                  $splittext .= '<span>';
                  $splittext .= $text;
                  $splittext .= '</span>';
              }else{
                  $splittext .= $text;
              }
              $output .= '<'.$texttag.' style="margin:'.$margin.';" class="'.$animation.' cq-animate-text" data-color="'.$color.'" data-background="'.$background.'" data-margin="'.$margin.'"  data-animation="'.$animation.'" data-fontsize="'.$fontsize.'">'.$splittext.'</'.$texttag.'>';

          }

          // if($link!="") $splittext .= '</a>';

          if($tostart=="on") $output .= $content;
          return $output;

        }

        add_shortcode('cq_vc_animatetext', 'cq_vc_animatetext_func');

      }
  }

}

?>
