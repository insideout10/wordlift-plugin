const path = require("path");

/**
 * See https://www.npmjs.com/package/whatwg-fetch#usage
 * See https://github.com/taylorhakes/promise-polyfill
 *
 * @type {{entry: string[], output: {filename: string, path: *}}}
 */

module.exports = {
  entry: {
    bundle: "./src/index.js"
  },
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "../../src/js/dist")
  }
};
