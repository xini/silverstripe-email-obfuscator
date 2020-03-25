(function($) {

	$(document).ready(function() {
		
		// rewrite hidden email addresses
		rewriteContactLinks();
		
	});
	
	function rewriteContactLinks() {
		$('a[data-eo]').each(function(){
			var sHref = $(this).data('eo');
			if(typeof(sHref) != 'undefined' && sHref != null) {
				var asErgebnis = sHref.match(/[a-zA-Z0-9_%\.\+\-]+\+[a-zA-Z0-9\.\-\+]+/);
				if (asErgebnis && asErgebnis.length > 0) {
					sHref = asErgebnis[0];
					sHref = sHref.rot13();
					sHref = sHref.replace(/\+/i, '@');
					sHref = sHref.replace(/\+/gi, '.');
					$(this).attr('href', 'mailto:' + sHref);
				}
			}
			if ($(this).hasClass('replacetext')) {
				$(this).html(sHref);
			}
			$(this).removeAttr('title');
			$(this).removeAttr('data-eo');
			$(this).removeClass('replacetext');
		});
	}

	String.prototype.rot13 = function(){
	    return this.replace(/[a-zA-Z]/g, function(c){
	        return String.fromCharCode((c <= "Z" ? 90 : 122) >= (c = c.charCodeAt(0) + 13) ? c : c - 26);
	    });
	};
	
}(jQuery));
