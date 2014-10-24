(function($){
	var initLayout = function() {
		var hash = window.location.hash.replace('#', '');
		var currentTab = $('ul.navigationTabs a')
							.bind('click', showTab)
							.filter('a[rel=' + hash + ']');
		if (currentTab.size() == 0) {
			currentTab = $('ul.navigationTabs a:first');
		}
		showTab.apply(currentTab.get(0));
		$('a.lightsGal').zoomimage();
		$('a.bwGal').zoomimage({
			border: 20,
			centered: true,
			hideSource: true
		});
		$('a.customGal').zoomimage({
			controlsTrigger: 'mouseover',
			className: 'custom',
			shadow: 40,
			controls: false,
			opacity: 1,
			beforeZoomIn: function(boxID) {
				$('#' + boxID)
					.find('img')
					.css('opacity', 0)
					.animate(
						{'opacity':1},
						{ duration: 500, queue: false }
					);
			},
			beforeZoomOut: function(boxID) {
				$('#' + boxID)
					.find('img')
					.css('opacity', 1)
					.animate(
						{'opacity':0},
						{ duration: 500, queue: false }
					);
			}
		});
	};
	
	var showTab = function(e) {
		var tabIndex = $('ul.navigationTabs a')
							.removeClass('active')
							.index(this);
		$(this)
			.addClass('active')
			.blur();
		$('div.zooimage').zoomimageClear();
		$('div.tab')
			.hide()
				.eq(tabIndex)
				.show();
	};
	
	EYE.register(initLayout, 'init');
})(jQuery)