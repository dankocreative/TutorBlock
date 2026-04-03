const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const path = require( 'path' );

module.exports = {
	...defaultConfig,
	entry: {
		'blocks/course-carousel/index':       path.resolve( __dirname, 'src/blocks/course-carousel/index.js' ),
		'blocks/course-grid/index':           path.resolve( __dirname, 'src/blocks/course-grid/index.js' ),
		'blocks/course-preview/index':        path.resolve( __dirname, 'src/blocks/course-preview/index.js' ),
		'blocks/category-course-grid/index':  path.resolve( __dirname, 'src/blocks/category-course-grid/index.js' ),
		'blocks/instructor-profile/index':    path.resolve( __dirname, 'src/blocks/instructor-profile/index.js' ),
		'blocks/course-stats/index':          path.resolve( __dirname, 'src/blocks/course-stats/index.js' ),
		'blocks/enrollment-cta/index':        path.resolve( __dirname, 'src/blocks/enrollment-cta/index.js' ),
	},
	output: {
		...defaultConfig.output,
		path: path.resolve( __dirname, 'build' ),
	},
};
