// PAI v2.0.0
// 03.07.11


(function(window) {
	var PAI = window['PAI'];
	
	if (!PAI['adapter']) { PAI['adapter'] = {}; }
	
	jQuery.extend(PAI['adapter'], {
		findAll: 	function(sel) { return jQuery(sel).get(); },
	
		addClassName: 	function(elm, cn) { $(elm).addClass(cn); },
		removeClassName: function(elm, cn) { $(elm).removeClass(cn); }
	});
	
}(window));
