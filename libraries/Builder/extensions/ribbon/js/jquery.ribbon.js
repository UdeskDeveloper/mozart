jQuery(document).ready(function($) {
	$('.cq-ribbon-container').each(function(index) {
		var _ribbonwidth = $(this).data('ribbonwidth');
		var _ribbontop = $(this).data('ribbontop') || 15;
		var _ribbonleft = $(this).data('ribbonleft') || -30;
		var _ribboncolor = $(this).data('ribboncolor');
		var _cqribbon = $(this).find('.cq-ribbon');

		if(!$(this).find('p')[0]){
			$(this).css('box-shadow', 'none');
		}

		_cqribbon.css({
			'width': _ribbonwidth,
			'height': _ribbonwidth
		});
		var _cqribbonbg = $(this).find('.cq-ribbon-bg');
		_cqribbonbg.css({
			// 'background': '#333',
			'top': _ribbontop,
			'left': _ribbonleft,
			'width': _ribbonwidth
		});
		_cqribbonbg.find('a').css('color', _ribboncolor);
		if(_cqribbon.hasClass('left')){
			$(this).find('.cq-ribbon-bg').css({
				// 'top': _ribbontop,
				'left': _ribbonleft
			});
		}else{

		}

	});
});
