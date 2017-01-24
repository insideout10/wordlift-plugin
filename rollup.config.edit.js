/**
 * Configuration Scripts: Rollup WordLift Admin.
 *
 * Define the rollup configuration to build WordLift's javascript files for the
 * admin area.
 *
 * Learn more about Rollup here: Learn rollup on
 * https://code.lengstorf.com/learn-rollup-js/
 *
 * @since 3.10.0
 */

// Rollup plugins
import eslint from 'rollup-plugin-eslint';
import babel from 'rollup-plugin-babel';
import nodeResolve from 'rollup-plugin-node-resolve';
import commonjs from 'rollup-plugin-commonjs';
import replace from 'rollup-plugin-replace';
import uglify from 'rollup-plugin-uglify';
import { resolve } from 'path';

// We need to tweak the location for the styled-components file, since
// jsnext:main is going to point to the `styled-components.es.js` file which
// raises an error of `Component` not found in `React`.
//
// https://github.com/styled-components/styled-components/issues/87#issuecomment-253987037
const styledComponentsDist = resolve( __dirname, 'node_modules/styled-components/dist/styled-components.js' )

export default {
	// The input file.
	entry: 'src/scripts/admin/edit.js',
	// The destination file.
	dest: 'src/admin/js/edit.min.js',
	// Make a closure around our own code, suitable for in-browser usage, see
	// more options here:
	// https://github.com/rollup/rollup/wiki/JavaScript-API#format
	format: 'iife',
	sourceMap: 'inline',
	plugins: [
		// Use the babel plugin to transpile to old JavaScript.
		babel( {
				   exclude: 'node_modules/**',
			   } ),
		nodeResolve( {
						 jsnext: true,
						 main: true,
						 browser: true
					 } ),
//		eslint( {
//					exclude: [
//						'src/css/**',
//					]
//				} ),
		{
			resolveId ( importee ) {
				// For more details, see the `styledComponentsDist` comments.
				if ( importee === 'styled-components' ) {
					return styledComponentsDist;
				}
			}
		},
		commonjs( {
					  include: 'node_modules/**',
					  namedExports: {
						  'node_modules/react/react.js': [
							  'PropTypes',
							  'createElement',
							  'Children',
							  'Component'
						  ]
					  }
				  } ),
		replace( {
					 // Replace `process.env.NODE_ENV` in React with production.
					 'process.env.NODE_ENV': JSON.stringify( 'production' )
				 } ),
		uglify( {
					compress: {
						screw_ie8: true,
						warnings: false
					},
					output: {
						comments: false
					}
//					, sourceMap: false
				} )
	],
};
