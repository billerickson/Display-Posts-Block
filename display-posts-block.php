<?php
/**
 * Plugin Name: Display Posts - Gutenberg Block
 * Plugin URI: https://github.com/billerickson/Display-Posts-Block/
 * Description: Adds Gutenberg block support to Display Posts Shortcode
 * Version: 1.0.0
 * Author: Bill Erickson
 * Author URI: https://www.billerickson.net
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Display Posts Block
 * @version 1.0.0
 * @author Bill Erickson <bill@billerickson.net>
 * @copyright Copyright (c) 2019, Bill Erickson
 * @link https://github.com/billerickson/Display-Posts-Block/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

use Carbon_Fields\Block;
use Carbon_Fields\Field;

class Display_Posts_Block {

	public function __construct() {

		add_action( 'after_setup_theme', array( $this, 'load_carbon_fields' ) );
		add_action( 'carbon_fields_register_fields', array( $this, 'register_carbon_fields' ) );
		add_action( 'wp_footer', array( $this, 'layout_options' ) );

	}

	/**
	 * Load Carbon Fields
	 *
	 */
	public function load_carbon_fields() {
		require_once( 'vendor/autoload.php' );
		\Carbon_Fields\Carbon_Fields::boot();
	}

	/**
	 * Register Carbon Fields
	 *
	 */
	public function register_carbon_fields() {

		$block = Block::make( __( 'Display Posts' ) )
			->set_category( 'widgets' )
			->set_preview_mode( true )
			->set_render_callback( function( $fields, $attributes, $inner_blocks ) {
				?>
		       <div class="block">
		            <div class="block__heading">
		                <h1><?php echo esc_html( $fields['heading'] ); ?></h1>
		            </div><!-- /.block__heading -->

		            <div class="block__content">
		                <?php echo apply_filters( 'the_content', $fields['content'] ); ?>
		            </div><!-- /.block__content -->
		        </div><!-- /.block -->
				<?php
			} );

		// Layout Options
		$block->add_fields( array(
			Field::make( 'select', 'layout', __( 'Layout' ) )
				->set_options( $this->layout_options() )
		));

	}

	/**
	 * Layout Options
	 *
	 */
	public function layout_options() {
		$layouts = array(
			'ul' => 'Bulleted List',
			'ol' => 'Numbered List',
			'div' => 'Unstyled',
		);

		$partials_path = apply_filters( 'display_posts_block_partials_path', get_stylesheet_directory() . '/partials' );
		$partials_prefix = apply_filters( 'display_posts_block_partials_prefix', 'archive' );

		if( false === $partials_path )
			return $layouts;

		$partials = scandir( $partials_path );
		$partials = array_diff( $partials, array( '..', '.' ) );
		if( false !== $partials_prefix ) {
			foreach( $partials as $i => $partial ) {
				if( 0 !== strpos( $partial, $partials_prefix ) )
					unset( $partials[ $i ] );
			}
		}

		if( !empty( $partials ) ) {
			foreach( $partials as $partial ) {
				$label = ucwords( str_replace( array( '.php', '-' ), array( '', ' ' ), $partial ) );
				$layouts[ $partial ] = $label;
			}
		}

		$layouts = apply_filters( 'display_posts_block_layouts', $layouts );
		return $layouts;
	}

}
new Display_Posts_Block();
