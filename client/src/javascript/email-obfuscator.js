;(function () {
	'use strict';

	function rewriteEmailLinks() {
		console.log('called');
		
		var links = document.querySelectorAll('a[data-eo]');
		Array.prototype.forEach.call(links, function(link, i) {
			var href = link.getAttribute('data-eo');
			console.log(href);
			if( href && href.indexOf('@') < 0 ) {
				var found = href.match(/[a-zA-Z0-9_%\.\+\-]+#[a-zA-Z0-9\.\-\+#]+/);
				if (found && found.length > 0) {
					href = found[0];
					href = href.rot13();
					href = href.replace(/#/i, '@');
					href = href.replace(/#/gi, '.');
					link.setAttribute('href', 'mailto:' + href);
				}
			}
			if (link.hasAttribute('data-eo-text')) {
				link.textContent = href;
			}
			link.removeAttribute('title');
			link.removeAttribute('data-eo');
			link.removeAttribute('data-eo-text');
		});
		
	}
	
	String.prototype.rot13 = function(){
	    return this.replace(/[a-zA-Z]/g, function(c){
	        return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
	    });
	};

	if (document.readyState === "loading") {
		// Loading hasn't finished yet
		document.addEventListener("DOMContentLoaded", rewriteEmailLinks);
	} else {
		// `DOMContentLoaded` has already fired
		rewriteEmailLinks();
	}

}());
