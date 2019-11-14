const path = require( 'path' );

module.exports = {
	entry: {
		mapping: './assets/src//js/mapping.js',
	},
	output: {
		path: path.resolve( __dirname, 'assets/dist/js' ),
		filename: '[name].js',
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader'
			}
		]
	}
};
