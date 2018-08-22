/**
 * This is the new entry point for JavaScript development. The idea is to migrate
 * the initial eject react-app to this webpack configuration.
 *
 * @since 3.19.0
 */
const path = require("path");
const webpack = require("webpack");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

/**
 * See https://www.npmjs.com/package/whatwg-fetch#usage
 * See https://github.com/taylorhakes/promise-polyfill
 *
 * @type {{entry: string[], output: {filename: string, path: *}}}
 */

module.exports = {
  entry: {
    bundle: "./src/Public/index.js",
    edit: "./src/Edit/index.js"
  },
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "../../src/js/dist")
  },
  devtool: "source-map",
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: "babel-loader?cacheDirectory",
          options: {
            presets: ["babel-preset-env"]
          }
        }
      },
      {
        test: /\.s?css$/,
        use: [
          // @see https://webpack.js.org/loaders/sass-loader/#extracting-style-sheets
          process.env.NODE_ENV !== "production"
            ? "style-loader"
            : MiniCssExtractPlugin.loader,
          "css-loader", // translates CSS into CommonJS
          "sass-loader" // compiles Sass to CSS, using Node Sass by default
        ]
      }
    ]
  },
  plugins: [
    // @see https://webpack.js.org/loaders/sass-loader/#extracting-style-sheets
    new MiniCssExtractPlugin({
      filename: "[name].css"
    }),
    new webpack.DefinePlugin({})
  ]
};
