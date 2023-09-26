function wlOpenFullscreenIframe(src) {
	var iframe                   = document.createElement( 'iframe' );
	iframe.src                   = src;
	iframe.style.position        = 'fixed';
	iframe.style.top             = '0';
	iframe.style.left            = '0';
	iframe.style.width           = '100%';
	iframe.style.height          = '100%';
	iframe.style.backgroundColor = 'none transparent';
	document.body.appendChild( iframe );
	iframe.addEventListener('load', function () {
		// When the zindex is set immediately it causes flicker when It's attached to document.
		iframe.style.zIndex          = 99999999;
	})
	iframe.contentWindow.addEventListener(
		"WL_ANGULAR_APP_CLOSE",
		function () {
			iframe.parentNode.removeChild( iframe );
		}
	)
	iframe.contentWindow.addEventListener(
		"WL_ANGULAR_APP_RELOAD",
		function () {
			iframe.parentNode.removeChild( iframe );
			wp.media.frame.close()
		}
	)
}
