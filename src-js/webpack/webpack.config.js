/**
 * This is the default wp-scripts Webpack configuration that we use as a base for ours.
 *
 * @since 3.23.0
 */
const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const DependencyExtractionWebpackPlugin = require("@wordpress/dependency-extraction-webpack-plugin");

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

const webpackConfig = {
  ...defaultConfig,
  entry: {
    bundle: "./src/Public/index.js",
    edit: "./src/Edit/index.js",
    "ingredients-meta-box": "./src/ingredients/meta-box.js",
    term: "./src/Term/index.js",
    "vocabulary-term": "./src/vocabulary-term/index.js",
    "block-editor": "./src/block-editor/index.js",
    "tiny-mce": "./src/tiny-mce/index.js",
    "wordlift-cloud": "./src/cloud/index.js",
    mappings: "./src/mappings/index.js",
    "mappings-edit": "./src/mappings/edit-mappings.js",
    // The sameAs autocomplete for the meta-box. This bundle is used for the meta-box in Block Editor.
    "autocomplete-select": "./src/Edit/index.autocomplete-select.js",
    "main-ingredient-select": "./src/Edit/index.main-ingredient-select.js",
    faq: "./src/faq/index.js",
    "tinymce-faq-plugin": "./src/faq/hooks/tinymce/tinymce-faq-plugin.js",
    "block-editor-faq-plugin": "./src/faq/hooks/block-editor/block-editor-faq-plugin.js",
    "post-excerpt": "./src/post-excerpt/index.js",
    "vocabulary": "./src/vocabulary/index.js",
    "vocabulary-term-page": "./src/vocabulary/screens/term-page/index.js",
    "vocabulary-settings-page": "./src/vocabulary/screens/settings/index.js",
    "videoobject" : "./src/videoobject/index.js",
    "no-editor-analysis" : "./src/no-editor-analysis/index.js"
  },
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "../../src/js/dist")
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
  ],
  externals: {
    ...defaultConfig.externals,
    backbone: "Backbone"
  }
};

// Create the full configuration for files that will include all the dependencies to keep WordPress pre 5.0.0 compatibility.
const webpackConfigFull = { ...webpackConfig };
// Remove the `DependencyExtractionWebpackPlugin`.
webpackConfigFull.plugins = webpackConfig.plugins.filter(
  entry => !(entry instanceof DependencyExtractionWebpackPlugin)
);

// Add the .full suffix to each entry.
webpackConfigFull.entry = Object.keys(webpackConfig.entry)
  .filter(entry => -1 === ["autocomplete-select", "block-editor"].indexOf(entry))
  .reduce((acc, key) => {
    acc[key + ".full"] = webpackConfig.entry[key];
    return acc;
  }, {});

// Finally export the WP 5 and WP pre-5 configurations.
module.exports = [webpackConfig, webpackConfigFull];
