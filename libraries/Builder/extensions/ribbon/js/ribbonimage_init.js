jQuery('.ribbon-image').each(function() {
	var _float = jQuery(this).data('float');
	var _zindex = jQuery(this).data('zindex');
	jQuery(this).fluidbox({
		// stackIndex: _zindex
		stackIndex: 1000
	});
});
