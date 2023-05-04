function wlOpenFullscreenIframe(src) {
	var iframe                   = document.createElement( 'iframe' );
	iframe.src                   = src;
	iframe.style.position        = 'fixed';
	iframe.style.top             = '0';
	iframe.style.left            = '0';
	iframe.style.width           = '100%';
	iframe.style.height          = '100%';
	iframe.style.zIndex          = 99999999;
	iframe.style.backgroundColor = 'none transparent';
	document.body.appendChild( iframe );
	iframe.contentWindow.addEventListener(
		"WL_ANGULAR_APP_CLOSE",
		function () {
			iframe.parentNode.removeChild( iframe );
		}
	)
	iframe.contentWindow.addEventListener(
		"WL_ANGULAR_APP_RELOAD",
		function () {
			// Just reload the media frame.
			if ( wp && wp.media && wp.media.frame) {
				wp.media.frame.close()
				wp.media.frame.open()
			}
		}
	)
}
