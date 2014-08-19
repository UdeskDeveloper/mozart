/**
 *	fires when the dom is ready
 *
 */
jQuery(document).ready(function() {
	addMenuDeleteButton();
	catchAjaxRequest();
});

/**
 * add Menu Delete Button
 *
 */
function addMenuDeleteButton() {
	jQuery('#menu-to-edit li.menu-item').each(function(){
		
		var delete_el 	= jQuery('.menu-item-actions a.item-delete', this);
		var anchor 		= '<a class="top-item-delete" id="' + delete_el.attr('id') + '" href="' + delete_el.attr('href') + '">' + delete_el.text() + '</a>';
		var controls	= jQuery('.item-controls .item-type', this);
		var count		= jQuery('a.top-item-delete', controls).size();
		
		// delete button already present?
		if ( count == 0 ) {
			controls.prepend( anchor + ' | ');
		}
	});
}

/**
 * catch Ajax Request when adding menu items
 *
 */
function catchAjaxRequest() {
	// after request
	jQuery(document).ajaxComplete(function(e, xhr, settings) {	
		if ( ( xhr.statusText == 'success' || xhr.statusText == 'OK' ) && settings.data.indexOf('action=add-menu-item') >= 0 ) {
			addMenuDeleteButton();
		}		
	});
}