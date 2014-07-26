/*global redux_change, wp, redux*/

(function( $ ) {
    "use strict";

    redux.field_objects = redux.field_objects || {};
    redux.field_objects.checkbox = redux.field_objects.checkbox || {};

    $( document ).ready(
        function() {
            //redux.field_objects.checkbox.init();
        }
    );

    redux.field_objects.checkbox.init = function( selector ) {
        if ( !selector ) {
            selector = $( document ).find( '.redux-container-checkbox' );
        }

        $( selector ).each(
            function() {
                var el = $( this );
                var parent = el;
                if ( !el.hasClass( 'redux-field-container' ) ) {
                    parent = el.parents( '.redux-field-container:first' );
                }
                if ( parent.hasClass( 'redux-field-init' ) ) {
                    parent.removeClass( 'redux-field-init' );
                } else {
                    return;
                }

                // Remove the image button
                el.find( '.checkbox' ).bind( 'click' ).on(
                    'click', function( e ) {
                        var val = 0;
                        if ($(this).is(':checked')) {
                            val = $(this).parent().find('.checkbox-check' ).attr('data-val');
                        }
                        $(this).parent().find('.checkbox-check').val(val);
                    }
                );
            }
        );
    };
})( jQuery );