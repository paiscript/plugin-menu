PAI(function() { 
	var adpt = PAI['adapter'],
		current = PAI['PAGE'],
		options = PAI['getOptions']()['plugins']['menu'];

	adpt.addEvent(document, 'pai:pageload', function() {
		var name, opt, cnCurrent, cnChildCurrent, element, list, i,
			pageinfo = PAI['pageInfo']();
		
		
		for(name in options['list']) {
			opt = options['list'][name];
			
			cnCurrent = (opt['currentClass'] || options['currentClass'] || 'pai_menu-current');
			cnChildCurrent = (opt['currentChildClass'] || options['currentChildClass'] || 'pai_menu-child_current');
			
			// remove old classess
			adpt['removeClassName'](adpt['find']('#pai_menu-' + name + ' .' + cnCurrent), cnCurrent);
			

			list = adpt['findAll']('#pai_menu-' + name + ' .' + cnChildCurrent);
			i = list.length;
			while(i--) {
				adpt['removeClassName'](list[i], cnChildCurrent)
			}
			
			
			element = adpt['find']('.pai_menu-page-' + (pageinfo['menu'] && pageinfo['menu'][name]['item'] || PAI.PAGE).split('/').join('_'));
			if (element) {
				adpt['addClassName'](element, cnCurrent);
			
				while(element = element.parentNode) {
					if (element.nodeName == 'LI') {
						adpt['addClassName'](element, cnChildCurrent);
					} else if (element.id == 'pai_menu-' + name) {
						break;
					}
				} // while
			} // if
		} // for
		
	});

});
