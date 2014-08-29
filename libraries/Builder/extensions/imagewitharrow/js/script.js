jQuery(document).ready(function($) {
	$('.cq-imgwitharrow-container').each(function(index) {
		var _this = $(this);
		var _textcolor = $(this).data('color');
		var _textbg = $(this).data('background');
		var _captionalign = $(this).data('captionalign');
		var _arrowleft = $(this).data('arrowleft');
		var _arrowtop = $(this).data('arrowtop');
		var _fontsize1 = $(this).data('fontsize1') || '13px';
		var _fontsize2 = $(this).data('fontsize2') || '11px';
		var _photo = $(this).find('.cq-imgwitharrow-photo');
		var _content = $(this).find('.cq-imgwitharrow-content');
		var _box = $(this).find('.cq-imgwitharrow-box');
		var _imageurl = _photo.data('url');
		var _iwidth = _this.data('iwidth');
		var _twidth = _this.data('twidth');
		var _theight = _this.data('theight');
		if(_imageurl!=""){
			_photo.css('background', 'url(' + _imageurl + ')');
			_photo.css({
				'background': 'url(' + _imageurl + ')',
				'background-size': 'cover',
				'background-position': 'center center'
			});
		}
		if(_textcolor){
			_content.css('color', _textcolor);
			$(this).find('.cq-arrowborder1, .cq-arrowborder3').css({
				'background': _textbg
			});
			// if(_captionalign=="right"){
			// 	$(this).find('.cq-arrowborder2').css({
			// 		'border-top': '10px solid ' + _textbg,
			// 		'border-left': '10px solid transparent',
			// 		'border-bottom': '10px solid ' + _textbg,
			// 		'border-right': '10px solid ' + _textbg
			// 	});
			// }
			if(_captionalign=="right"){
				$(this).find('.cq-arrowborder1').css('height', _arrowtop);
				$(this).find('.cq-arrowborder2').css({
					'border-top': '10px solid ' + _textbg,
					'border-left': '10px solid transparent',
					'border-bottom': '10px solid ' + _textbg,
					'border-right': '10px solid ' + _textbg
				});
				_box.css({
					'left': 'calc(' + _iwidth + ' - 10px)',
					'width': 'calc(' + _twidth + ' + 10px)'
				});
			}
			if(_captionalign=="left"){
				$(this).find('.cq-arrowborder1').css('height', _arrowtop);
				$(this).find('.cq-arrowborder2').css({
					'border-top': '10px solid ' + _textbg,
					'border-left': '10px solid ' + _textbg,
					'border-bottom': '10px solid ' + _textbg,
					'border-right': '10px solid transparent'
				});
				_photo.css({'left': 'calc(' + _twidth + ' - 10px)' });

			}
			if(_captionalign=="top"){
				$(this).find('.cq-arrowborder1').css('width', _arrowleft);
				if(_arrowleft>0) $(this).find('.cq-arrowborder3').css('width', 'calc(100% - ' + _arrowleft + 'px - 20px)');
				else $(this).find('.cq-arrowborder3').css('width', 'calc(100% - ' + _arrowleft + ' - 20px)');
				$(this).find('.cq-arrowborder2').css({
					'border-top': '10px solid ' + _textbg,
					'border-left': '10px solid ' + _textbg,
					'border-bottom': '10px solid transparent',
					'border-right': '10px solid ' + _textbg
				});
				_box.css({
					'height': 'calc(' + _theight + ' + 10px)'
				});
			}
			if(_captionalign=="bottom"){
				$(this).find('.cq-arrowborder1').css('width', _arrowleft);
				if(_arrowleft>0) $(this).find('.cq-arrowborder3').css('width', 'calc(100% - ' + _arrowleft + 'px - 20px)');
				else $(this).find('.cq-arrowborder3').css('width', 'calc(100% - ' + _arrowleft + ' - 20px)');
				$(this).find('.cq-arrowborder2').css({'border-top': '10px solid transparent',
					'border-left': '10px solid ' + _textbg,
					'border-bottom': '10px solid ' + _textbg,
					'border-right': '10px solid ' + _textbg
				});
			}
		}

		// _this.find('a.cq-lightbox').attr('rel', 'gallery');
		_this.find('a.cq-lightbox').boxer({
	    	fixed: true
	    });


		function _resizeFont(){
			if(_this.width()<=480){
				_this.find('.cq-imgwitharrow-content p').css('font-size', _fontsize2);
				// _this.find('.cq-imgwitharrow-content h2, .cq-imgwitharrow-content h3, .cq-imgwitharrow-content h4, .cq-imgwitharrow-content h5').css('font-size', _fontsize2);
				// if(_captionalign=="left"||_captionalign=="right"){
					// _this.find('.cq-imgwitharrow-content').css('width', '64%');
				// }
			}else{
				_this.find('.cq-imgwitharrow-content p').css('font-size', _fontsize1);
				// _this.find('.cq-imgwitharrow-content h2, .cq-imgwitharrow-content h3, .cq-imgwitharrow-content h4, .cq-imgwitharrow-content h5').css('font-size', '');
			}

		}
		_resizeFont();
		$(window).on('resize', _resizeFont);


	});
});
