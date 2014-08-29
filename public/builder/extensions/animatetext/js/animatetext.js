jQuery(document).ready(function($) {
	$('.cq-animate-text').each(function() {
		var _color = $(this).data('color');
		var _background = $(this).data('background');
		var _margin = $(this).data('margin');
		var _padding = $(this).data('padding');
		var _animation = $(this).data('animation');
		var _fontsize = $(this).data('fontsize');
		var _spanlength = $(this).find('span').length;
		if(_animation=="animation-02" || _animation=="animation-03") {
			$(this).find('span').each(function(index) {
				$(this).css({
					'display': 'inline-block',
					'background': _background,
					'font-size': _fontsize,
					'color': _color,
					'margin': _margin,
					'padding': _padding
				});
			});

		}else{
			if(_animation=="animation-08"){
				$(this).css({
					'overflow': 'hidden'
				})
			}
			$(this).css({
				// 'display': 'inline-block',
				'background': _background,
				'font-size': _fontsize,
				'color': _color,
				'margin': _margin
			});
		}

	});
});
