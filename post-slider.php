<?php
/**
 * Plugin Name:       Post Slider
 * Description:       A WordPress block that provides similar functionality to the Post Template, but displays posts in a slider/carousel format.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Mark Honeychurch
 * Author URI:        https://mark.honeychurch.org
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-slider
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants used by asset registration/enqueue.
// (Removed) No plugin-level constants required.
/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
function create_block_post_slider_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'create_block_post_slider_block_init' );

/**
 * Modify block registration to attach a PHP render callback when using metadata collection.
 * This keeps the modern manifest-based framework while enabling dynamic rendering.
 */
add_filter( 'register_block_type_args', function( $args, $name ) {
	if ( 'markhoney/post-slider' === $name ) {
		$args['render_callback'] = 'post_slider_render_callback';
	}
	return $args;
}, 10, 2 );

/**
 * Register frontend Swiper assets (not enqueued by default).
 */
function post_slider_register_assets() {
	// CSS
	wp_register_style(
		'swiper',
		plugin_dir_url( __FILE__ ) . 'build/post-slider/swiper/swiper-bundle.min.css',
		array(),
		'11.2.10'
	);

	// JS
	wp_register_script(
		'swiper',
		plugin_dir_url( __FILE__ ) . 'build/post-slider/swiper/swiper-bundle.min.js',
		array(),
		'11.2.10',
		true
	);
}
add_action( 'init', 'post_slider_register_assets' );

/**
 * Enqueue Swiper only on pages where the block is present.
 */
function post_slider_enqueue_assets() {
	if ( has_block( 'markhoney/post-slider' ) ) {
		wp_enqueue_style( 'swiper' );
		wp_enqueue_script( 'swiper' );
	}
}
add_action( 'wp_enqueue_scripts', 'post_slider_enqueue_assets' );

/**
 * Server-side render callback for the Post Slider block.
 */
function post_slider_render_callback( $attributes, $content, $block ) {
	if ( ! isset( $block->context['queryId'] ) ) {
		return '';
	}

	$page_key = isset( $block->context['queryId'] ) ? 'query_' . $block->context['queryId'] . '_page' : 'query_page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : (int) $_GET[ $page_key ];

	$query_args = build_query_vars_from_query_block( $block, $page );

	$per_page = isset( $block->context['query']['perPage'] ) ? $block->context['query']['perPage'] : 10;
	$query_args['posts_per_page'] = $per_page;

	$query = new WP_Query( $query_args );

	if ( ! $query->have_posts() ) {
		return '';
	}

	$swiper_config = array(
		'slidesPerView'  => isset( $attributes['slidesPerView'] ) ? $attributes['slidesPerView'] : 1,
		'spaceBetween'   => isset( $attributes['spaceBetween'] ) ? $attributes['spaceBetween'] : 30,
		'speed'          => isset( $attributes['speed'] ) ? $attributes['speed'] : 300,
		'loop'           => isset( $attributes['loop'] ) ? $attributes['loop'] : true,
		'centeredSlides' => isset( $attributes['centeredSlides'] ) ? $attributes['centeredSlides'] : false,
	);

	if ( ! empty( $attributes['autoplay'] ) ) {
		$swiper_config['autoplay'] = array(
			'delay'               => isset( $attributes['autoplayDelay'] ) ? $attributes['autoplayDelay'] : 3000,
			'disableOnInteraction' => false,
		);
	}

	if ( ! empty( $attributes['navigation'] ) ) {
		$swiper_config['navigation'] = array(
			'nextEl' => '.swiper-button-next',
			'prevEl' => '.swiper-button-prev',
		);
	}

	if ( ! empty( $attributes['pagination'] ) ) {
		$pagination_type              = isset( $attributes['paginationType'] ) ? $attributes['paginationType'] : 'bullets';
		$swiper_config['pagination'] = array(
			'el'        => '.swiper-pagination',
			'clickable' => true,
			'type'      => $pagination_type,
		);
	}

	if ( ! empty( $attributes['effect'] ) && 'slide' !== $attributes['effect'] ) {
		$swiper_config['effect'] = $attributes['effect'];
		if ( in_array( $attributes['effect'], array( 'fade', 'cube', 'flip' ), true ) ) {
			$swiper_config['slidesPerView'] = 1;
		}
	}

	if ( ! empty( $attributes['enableBreakpoints'] ) ) {
		$swiper_config['breakpoints'] = array(
			640  => array( 'slidesPerView' => 1 ),
			768  => array( 'slidesPerView' => 2 ),
			1024 => array( 'slidesPerView' => 3 ),
		);
	}

	if ( ! empty( $attributes['customParams'] ) ) {
		$custom_params = json_decode( $attributes['customParams'], true );
		if ( is_array( $custom_params ) ) {
			$swiper_config = array_merge( $swiper_config, $custom_params );
		}
	}

	$slider_id = 'post-slider-' . wp_unique_id();

	$wrapper_attributes = get_block_wrapper_attributes( array(
		'class' => 'post-slider-wrapper',
	) );

	$output  = sprintf( '<div %s>', $wrapper_attributes );
	$output .= sprintf(
		'<div class="swiper post-slider %s" data-swiper-config=\'%s\'>',
		esc_attr( $slider_id ),
		esc_attr( wp_json_encode( $swiper_config ) )
	);
	$output .= '<div class="swiper-wrapper">';

	while ( $query->have_posts() ) {
		$query->the_post();

		$output .= '<div class="swiper-slide">';

		$filter_block_context = static function( $context ) {
			$context['postId']   = get_the_ID();
			$context['postType'] = get_post_type();
			return $context;
		};
		add_filter( 'render_block_context', $filter_block_context, 1 );

		if ( ! empty( $block->inner_blocks ) ) {
			foreach ( $block->inner_blocks as $inner_block ) {
				if ( is_object( $inner_block ) && method_exists( $inner_block, 'render' ) ) {
					$output .= $inner_block->render();
				} else {
					$output .= render_block( (array) $inner_block );
				}
			}
		} elseif ( ! empty( $content ) ) {
			$parsed_blocks = parse_blocks( $content );
			foreach ( $parsed_blocks as $parsed_block ) {
				if ( empty( $parsed_block['blockName'] ) ) {
					continue;
				}
				$output .= render_block( $parsed_block );
			}
		} else {
			if ( isset( $block->parsed_block['innerContent'] ) && is_array( $block->parsed_block['innerContent'] ) ) {
				foreach ( $block->parsed_block['innerContent'] as $inner_html ) {
					if ( is_string( $inner_html ) && ! empty( trim( $inner_html ) ) ) {
						$parsed = parse_blocks( $inner_html );
						foreach ( $parsed as $parsed_block ) {
							if ( empty( $parsed_block['blockName'] ) ) {
								continue;
							}
							$output .= render_block( $parsed_block );
						}
					}
				}
			}
			if ( isset( $block->parsed_block['innerBlocks'] ) && is_array( $block->parsed_block['innerBlocks'] ) ) {
				foreach ( $block->parsed_block['innerBlocks'] as $parsed_inner ) {
					$output .= render_block( $parsed_inner );
				}
			}
		}

		remove_filter( 'render_block_context', $filter_block_context, 1 );

		$output .= '</div>';
	}

	wp_reset_postdata();

	$output .= '</div>';

	if ( ! empty( $attributes['navigation'] ) ) {
		$output .= '<div class="swiper-button-next"></div>';
		$output .= '<div class="swiper-button-prev"></div>';
	}

	if ( ! empty( $attributes['pagination'] ) ) {
		$output .= '<div class="swiper-pagination"></div>';
	}

	$output .= '</div>';
	$output .= '</div>';

	$inline_script  = '(function(){';
	$inline_script .= 'function init(){';
	$inline_script .= 'var el=document.querySelector(".post-slider.' . esc_js( $slider_id ) . '");';
	$inline_script .= 'if(!el||typeof Swiper!=="function"){return}';
	$inline_script .= 'var cfg={};try{cfg=JSON.parse(el.dataset.swiperConfig||"{}")}catch(e){}';
	$inline_script .= 'new Swiper(el,cfg);';
	$inline_script .= '}';
	$inline_script .= 'if(document.readyState==="complete"){init()}else{window.addEventListener("load",init)}';
	$inline_script .= '})();';
	$output       .= '<script>' . $inline_script . '</script>';

	return $output;
}
