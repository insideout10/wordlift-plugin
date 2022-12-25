const { defineConfig } = require("cypress");

module.exports = defineConfig({
  "blockHosts": ["www.google-analytics.com", "ssl.google-analytics.com"],
  e2e: {
    baseUrl: 'https://wl-qa.mhimon.dev',
    experimentalStudio: true,
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
