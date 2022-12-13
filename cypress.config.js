const { defineConfig } = require("cypress");

module.exports = defineConfig({
  env: {
    wp_user: 'user',
    wp_pass: 'bitnami',
    version: '3.40.4',
  },
  e2e: {
    baseUrl: "https://wordlift.localhost",
    experimentalStudio: true,
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
