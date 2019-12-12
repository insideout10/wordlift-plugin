const path = require('path');

module.exports = {
  mode: "production", // "production" | "development" | "none"
  // Chosen mode tells webpack to use its built-in optimizations accordingly.
  entry:  {
    mappings: './mappings.js',
    edit_mappings:'./edit-mappings.js',
  },
  // defaults to ./src
  // Here the application starts executing
  // and webpack starts bundling

  output: {
    filename: '[name].js',
    path: path.resolve(__dirname, "../../../../js/dist")
  },

  module: {
    rules: [
      {
        test: /\.(js|jsx)$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
      }
    ],


  }
}
