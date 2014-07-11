(function($){
	
	
	function initialize_field( $el ) {

        $el = $el.find('.star_rating');
        var field_path = $el.attr('data-path');
        var field_target = $el.attr('data-target');
        var field = $el.attr('data-field');
        console.log($( field ));

        $( field ).raty({
            score: function() {
                return $(this).attr('data-score');
            },
            number: function() {
                return $(this).attr('data-number');
            },
            starHalf    : 'fa fa-star-half-o',                                // The name of the half star image.
            starOff     : 'fa fa-star-o',                                 // Name of the star image off.
            starOn      : 'fa fa-star',
            target: field_target,
            targetType: 'score',
            starType: 'i',
            targetKeep: true,
            half: true,
            path: field_path,
            click: function( score, evt ) {
                update_rating_average( score, $(this).closest('.inside') );
            }
        });
		
	}


    function update_rating_average( score, box ) {


        if ( $(box).find( '.star_rating_average' ).length >= 1 ) {

            var count = 0;

            var overall_score = 0;

            $(box).find('.star_input').each(function(){

                var grab_score = $(this).val();

                overall_score = overall_score + parseFloat( grab_score );

                count++;

            });

            overall_score = overall_score / count;

            $(box).find('.star_rating_average').find('.stars').raty('set', { score: overall_score });

        }

    }
	
	
	if( typeof acf.add_action !== 'undefined' ) {
	
		/*
		*  ready append (ACF5)
		*
		*  These are 2 events which are fired during the page load
		*  ready = on page load similar to $(document).ready()
		*  append = on new DOM elements appended via repeater field
		*
		*  @type	event
		*  @date	20/07/13
		*
		*  @param	$el (jQuery selection) the jQuery element which contains the ACF fields
		*  @return	n/a
		*/
		
		acf.add_action('ready append', function( $el ){
			
			// search $el for fields of type 'star_rating'
			acf.get_fields({ type : 'star_rating'}, $el).each(function(){
				
				initialize_field( $(this) );
				
			});
			
		});
		
		
	} else {
		
		
		/*
		*  acf/setup_fields (ACF4)
		*
		*  This event is triggered when ACF adds any new elements to the DOM. 
		*
		*  @type	function
		*  @since	1.0.0
		*  @date	01/01/12
		*
		*  @param	event		e: an event object. This can be ignored
		*  @param	Element		postbox: An element which contains the new HTML
		*
		*  @return	n/a
		*/
		
		$(document).live('acf/setup_fields', function(e, postbox){
			
			$(postbox).find('.field_type-star_rating').each(function(){

				initialize_field( $(this) );
				
			});
		
		});
	
	
	}


})(jQuery);
