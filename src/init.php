<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * Assets enqueued:
 * 1. blocks.style.build.css - Frontend + Backend.
 * 2. blocks.build.js - Backend.
 * 3. blocks.editor.build.css - Backend.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function china_video_block_cvb_block_assets() { // phpcs:ignore
	// Register block styles for both frontend + backend.
	wp_register_style(
		'china_video_block-cgb-style-css', // Handle.
		plugins_url( 'dist/blocks.style.build.css', dirname( __FILE__ ) ), // Block style CSS.
		filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
	);

	// Register block editor script for backend.
	wp_register_script(
		'china_video_block-cgb-block-js', // Handle.
		plugins_url( '/dist/blocks.build.js', dirname( __FILE__ ) ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element' ), // Dependencies, defined above.
		filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
		true // Enqueue the script in the footer.
	);
	$options = get_option( 'cvb_options' );
	wp_localize_script(
		'china_video_block-cgb-block-js',
		'cvbPHPVars',
		array(
			'cvbIPInfoToken' => $options['cvb_ipinfo_token'] ?? '',
			'settingsURL'    => get_site_url() . '/wp-admin/options-general.php?page=cvb',
		)
	);

	// Register block editor styles for backend.
	wp_register_style(
		'china_video_block-cgb-block-editor-css', // Handle.
		plugins_url( 'dist/blocks.editor.build.css', dirname( __FILE__ ) ), // Block editor CSS.
		array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
		filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);

	/**
	 * Register Gutenberg block on server-side.
	 *
	 * Register the block on server-side to ensure that the block
	 * scripts and styles for both frontend and backend are
	 * enqueued when the editor loads.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
	 * @since 1.16.0
	 */
	register_block_type(
		'cvb/block-china-video-block',
		array(
			// Enqueue blocks.style.build.css on both frontend & backend.
			// 'style'         => 'china_video_block-cgb-style-css',
			// Enqueue blocks.build.js in the editor only.
			'editor_script' => 'china_video_block-cgb-block-js',
			// Enqueue blocks.editor.build.css in the editor only.
			'editor_style'  => 'china_video_block-cgb-block-editor-css',
		)
	);
}

// Hook: Block assets.
add_action( 'init', 'china_video_block_cvb_block_assets' );



/**
 * Enqueue Gutenberg block assets for frontend and backend.
 *
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function china_video_block_enqueue_assets() {
	if ( has_block( 'cvb/block-china-video-block' ) && ! is_admin() ) {
		wp_enqueue_script(
			'china_video_block-front-js',
			plugins_url( '/src/block/front.js', dirname( __FILE__ ) ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ),
			true
		);
	}
}

add_action( 'enqueue_block_assets', 'china_video_block_enqueue_assets' );
