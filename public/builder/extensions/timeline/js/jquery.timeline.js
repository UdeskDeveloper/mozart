jQuery(document).ready(function($){
	$('.cq-timeline-container').each(function(index) {
		var _container = $(this);
		var _datecolor = _container.data('datecolor');
		var _responsivewidth = _container.data('responsivewidth');

		function _resizer(){
				// _container.find('.cd-timeline-block:nth-child(even) .cd-timeline-content').each(function(index) {
				_container.find('.cd-timeline-content').each(function(index) {
					// $(this).addClass('pink');
					var _color = $(this).data('color');
					$(this).addClass(_color);
					$(this).find('cd-date').css('color', _datecolor)
				});
				if(_container.width()>=_responsivewidth){
					_container.find('.cq-timeline-border').addClass('largescreen');
					_container.find('.cd-timeline-img').each(function(index) {
						$(this).addClass('largescreen');
					});
					_container.find('.cd-timeline-content').each(function(index) {
						$(this).addClass('cq-border1 largescreen');
						$(this).find('a.wpb_button_a, a.vc_btn').css({
							'margin-top': 'auto',
							'float': 'left'
						}).appendTo($(this));
					});

					_container.find('.cd-timeline-block:nth-child(even) .cd-timeline-content').each(function(index) {
						$(this).css({
							'float': 'right'
						}).addClass('cq-border2');
						$(this).find('a.wpb_button_a, a.vc_btn').css({
							'float': 'right'
						});
					})
					_container.find('.cd-date').each(function() {
						$(this).css('color', _datecolor).addClass('pos1');
					});
					_container.find('.cd-timeline-block:nth-child(even) .cd-timeline-content .cd-date').each(function(index) {
						$(this).addClass('pos2')
					});
					// _container.find('.cd-timeline-block:nth-child(even) .cd-timeline-content .cd-date').css({
					// 	'left': 'auto',
					// 	'text-align': 'right',
					// 	'right': '133%'
					// });
				}else{

					_container.find('.cq-timeline-border').removeClass('largescreen');
					_container.find('.cd-timeline-img').each(function(index) {
						$(this).removeClass('largescreen');
					});

					var _contentcolor;
					_container.find('.cd-timeline-content').each(function(index) {
						_contentcolor = $(this).css('color');
						$(this).find('.cd-date').css('color', _contentcolor).removeClass('pos1 pos2');
						$(this).css({
							'float': 'left'
						});
						$(this).find('a.wpb_button_a, a.vc_btn').css({
							'margin-top': '20px',
							'float': 'right'
						});

					}).removeClass('cq-border1 cq-border2 largescreen');

				}

		}

		$(window).on('resize', _resizer);

	});

	$(window).trigger('resize');

});
