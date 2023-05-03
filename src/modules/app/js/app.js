function wlOpenFullscreenIframe(src) {
  var iframe = document.createElement('iframe');
  iframe.src = src;
  iframe.style.position = 'fixed';
  iframe.style.top = '0';
  iframe.style.left = '0';
  iframe.style.width = '100%';
  iframe.style.height = '100%';
  iframe.style.zIndex = 99999999;
  iframe.style.backgroundColor = 'none transparent';
  document.body.appendChild(iframe);
}
