(function ($, document, undefined) {

	var pluses = /\+/g;

	function raw(s) {
		return s;
	}

	function decoded(s) {
		return decodeURIComponent(s.replace(pluses, ' '));
	}

	var config = $.cookie = function (key, value, options) {

		// write
		if (value !== undefined) {
			options = $.extend({}, config.defaults, options);

			if (value === null) {
				options.expires = -1;
			}

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setDate(t.getDate() + days);
			}

			value = config.json ? JSON.stringify(value) : String(value);

			return (document.cookie = [
				encodeURIComponent(key), '=', config.raw ? value : encodeURIComponent(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// read
		var decode = config.raw ? raw : decoded;
		var cookies = document.cookie.split('; ');
		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			if (decode(parts.shift()) === key) {
				var cookie = decode(parts.join('='));
				return config.json ? JSON.parse(cookie) : cookie;
			}
		}

		return null;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.cookie(key) !== null) {
			$.cookie(key, null, options);
			return true;
		}
		return false;
	};

	$.cookieAccepter = function( url ) {
		url = ( typeof url === 'undefined') ? '/' : url;

		var inited = $('#cookie_accepter').html();

		if( typeof inited !== 'undefined' ) return false;

		var accept 	= $.cookie( '__acceptedcookie' );
		var text 	= '<div style="line-height: 16px; text-align: justify; padding-right: 10px;">A weboldalunk felületén süti (cookie) fájlokat használunk. Ezeket a fájlokat az Ön gépén tárolja a rendszer. A cookie-k személyek azonosítására nem alkalmasak, szolgáltatásaink biztosításához szükségesek. Az oldal használatával Ön beleegyezik a cookie-k használatába. További információért kérjük, olvassa el adatvédelmi szabályzatunkat.</div>';
		text += '<div style="padding-left: 10px; text-align:center; margin-top: 10px;">' +
			'<a href="'+url+'" style="background:#5a5a5a; color:white; padding: 4px; margin-bottom: 5px; text-decoration:none; box-sizing:border-box; line-height: 22px;" >Adatvédelmi szabályzat &gt;</a> '+
			'<a id="cookie_accepter_btn" style="background:rgb(205, 42, 48); color:white; padding: 4px; text-decoration:none; box-sizing:border-box; line-height: 22px;" href="javascript:void(0);">Elfogad &gt;</a></div>';

		if( !accept ) {
			$('body').append('<div id="cookie_accepter" style="position:fixed; box-sizing: border-box; z-index: 999999999; bottom:0; background: rgba(0, 122, 255, 0.94); font-size: 14px; color: #ffffff; padding: 1% 20%; width:100%;">'+text+'</div>');

			$('#cookie_accepter_btn').click( function() {
				$('#cookie_accepter').remove();
				$.cookie( '__acceptedcookie', true, { expires: 365, path: '/' } );
			} );
		}
	}
})(jQuery, document);
