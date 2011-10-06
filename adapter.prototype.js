// PAI v2.0.0
// 03.07.11


(function(window) {
	var PAI = window['PAI'];
	
	if (!PAI['adapter']) { PAI['adapter'] = {}; }
	
	Object.extend(PAI['adapter'], {
		findAll: 	window['Prototype']['Selector']['select'],
	
	
		addClassName: 	window['Element']['addClassName'],
		removeClassName: 	window['Element']['removeClassName']
	});
	
}(window));
