/**
 * This is the default wp-scripts Webpack configuration that we use as a base for ours.
 *
 * @since 3.23.0
 */
const defaultConfig = require("@wordpress/scripts/config/webpack.config");

/**
 * This is the new entry point for JavaScript development. The idea is to migrate
 * the initial eject react-app to this webpack configuration.
 *
 * @since 3.19.0
 */
const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

/**
 * See https://www.npmjs.com/package/whatwg-fetch#usage
 * See https://github.com/taylorhakes/promise-polyfill
 *
 * @type {{entry: string[], output: {filename: string, path: *}}}
 */

module.exports = {
  ...defaultConfig,
  entry: {
    bundle: "./src/Public/index.js",
    edit: "./src/Edit/index.js",
    term: "./src/Term/index.js",
    "block-editor": "./src/block-editor/index.js",
    "tiny-mce": "./src/tiny-mce/index.js",
    "wordlift-cloud": "./src/Cloud/index.js",
    "mappings": "./src/mappings/index.js"
  },
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "../../js/dist")
  },
  /*
   * Give precedence to our node_modules folder when resolving the same module.
   *
   * This solves duplicate issues with styled-components.
   *
   * @see https://www.styled-components.com/docs/faqs#why-am-i-getting-a-warning-about-several-instances-of-module-on-the-page
   */
  // resolve: {
  //   modules: [path.resolve(__dirname, "node_modules"), "node_modules"]
  // },
  devtool: "eval-source-map",
  module: {
    ...defaultConfig.module,
    rules: [
      ...defaultConfig.module.rules,
      {
        test: /\.css$/i,
        use: [MiniCssExtractPlugin.loader, "css-loader"]
      },
      {
        test: /\.s[ac]ss$/i,
        use: [
          // We use the MiniCssExtractPlugin for both production and development
          // since development happens inside of WordPress which loads the css
          // files anyway (using the enqueue_scripts hook).
          //
          // @see https://webpack.js.org/loaders/sass-loader/#extracting-style-sheets
          MiniCssExtractPlugin.loader,
          "css-loader",
          "sass-loader" // compiles Sass to CSS, using Node Sass by default
        ]
      },
      {
        test: /.svg$/,
        use: {
          loader: "svg-react-loader"
        }
      }
    ]
  },
  plugins: [
    ...defaultConfig.plugins,
    // @see https://webpack.js.org/loaders/sass-loader/#extracting-style-sheets
    new MiniCssExtractPlugin({
      filename: "[name].css"
    })
  ]
};
