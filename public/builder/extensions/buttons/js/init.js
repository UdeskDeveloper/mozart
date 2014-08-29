jQuery(document).ready(function($) {
	var touch = Modernizr.touch;
	$('.cq-parallaxcontainer').each(function(index) {
		var _this = $(this);
		$(this).find('.cq-parallaximage').imageScroll({
		  container: _this,
		  holderClass: 'cq-parallaximgholder',
		  imageAttribute: (touch === true) ? 'image-mobile' : 'image',
		  touch: touch
		});
	});

	$('.cq-parallaximgholder').each(function(index) {
		$(this).on('click', function(event) {
			var _target = $(this).data('target');
			var _link = $(this).data('link');
			if(_link&&_link!=""){
				if(_target=="_blank"){
					window.open(_link);
				}else{
					window.location = _link;
				}
			}

		});
	});

});
