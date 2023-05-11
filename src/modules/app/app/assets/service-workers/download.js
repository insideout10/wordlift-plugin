const VERSION = "v1";

self.addEventListener("install", (event) => {
  log("INSTALLING ");
  const installCompleted = Promise.resolve().then(() => log("INSTALLED"));

  event.waitUntil(installCompleted);
});

self.addEventListener("activate", (event) => {
  log("ACTIVATING");
  const activationCompleted = Promise.resolve().then((activationCompleted) =>
    log("ACTIVATED")
  );

  event.waitUntil(activationCompleted);
});

// handling service worker installation
const re =
  /^.*\/download(\/content-generation\/content-generations\/\d+\/completions)\?apiUrl=(.*)&token=(.*)$/;

self.addEventListener("fetch", (event) => {
  log("HTTP call intercepted - " + event.request.url);
  const matches = re.exec(event.request.url);
  if (null === matches) {
    return event.respondWith(fetch(event.request.url));
  } else {
    const path = matches[1];
    const apiUrl = decodeURIComponent(matches[2]);
    const token = decodeURIComponent(matches[3]);
    return event.respondWith(
      fetch(`${apiUrl}${path}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
    );
  }
});

// each logging line will be prepended with the service worker version
function log(message) {
  console.log(VERSION, message);
}
