document.addEventListener( 'DOMContentLoaded', function() {
	var el = document.querySelector('#wp-admin-bar-pantheon-hud');
	if ( ! el ) {
		return;
	}
	var fetchData = function() {
		if ( ! document.querySelector('#wp-admin-bar-pantheon-hud-wp-admin-loading') ) {
			return;
		}
		var request = new XMLHttpRequest();
		request.open('GET', pantheonHudData.requestUrl, true);
		request.onload = function() {
			if (this.status >= 200 && this.status < 400) {
				document.querySelector('#wp-admin-bar-pantheon-hud .ab-sub-wrapper').innerHTML = this.response;
				el.removeEventListener('mouseover', fetchData);
				el.removeEventListener('focus', fetchData);
			}
		};
		request.send();
	};
	el.addEventListener('mouseover', fetchData);
	el.addEventListener('focus', fetchData);
} );
