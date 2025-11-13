<?php
// This file is generated. Do not modify it manually.
return array(
	'query-loop-slider' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'markhoney/query-loop-slider',
		'version' => '0.1.0',
		'title' => 'Query Loop Slider',
		'category' => 'theme',
		'icon' => 'slides',
		'description' => 'A WordPress block that provides similar functionality to the Post Template, but displays posts in a slider/carousel format.',
		'example' => array(
			
		),
		'parent' => array(
			'core/query'
		),
		'supports' => array(
			'align' => true,
			'alignWide' => true,
			'anchor' => true,
			'ariaLabel' => true,
			'reusable' => false,
			'html' => false,
			'layout' => array(
				'allowSwitching' => false,
				'allowSizingOnChildren' => false,
				'allowInheriting' => false,
				'default' => array(
					'type' => 'constrained'
				)
			),
			'background' => array(
				'backgroundImage' => true,
				'backgroundSize' => true
			),
			'spacing' => array(
				'margin' => true,
				'padding' => true,
				'blockGap' => true
			),
			'color' => array(
				'gradients' => true,
				'link' => true,
				'background' => true,
				'text' => true
			),
			'dimensions' => array(
				'aspectRatio' => true,
				'minHeight' => true
			),
			'typography' => array(
				'fontSize' => true,
				'lineHeight' => true,
				'fontFamily' => true,
				'fontWeight' => true,
				'fontStyle' => true,
				'textTransform' => true,
				'textDecoration' => true,
				'letterSpacing' => true
			),
			'__experimentalBorder' => array(
				'color' => true,
				'radius' => true,
				'style' => true,
				'width' => true
			),
			'shadow' => true,
			'interactivity' => array(
				'clientNavigation' => true
			)
		),
		'attributes' => array(
			'slidesPerView' => array(
				'type' => 'number',
				'default' => 1
			),
			'spaceBetween' => array(
				'type' => 'number',
				'default' => 0
			),
			'autoplay' => array(
				'type' => 'boolean',
				'default' => false
			),
			'autoplayDelay' => array(
				'type' => 'number',
				'default' => 3000
			),
			'loop' => array(
				'type' => 'boolean',
				'default' => true
			),
			'navigation' => array(
				'type' => 'boolean',
				'default' => true
			),
			'pagination' => array(
				'type' => 'boolean',
				'default' => true
			),
			'paginationType' => array(
				'type' => 'string',
				'default' => 'bullets',
				'enum' => array(
					'bullets',
					'fraction',
					'progressbar'
				)
			),
			'speed' => array(
				'type' => 'number',
				'default' => 300
			),
			'effect' => array(
				'type' => 'string',
				'default' => 'slide',
				'enum' => array(
					'slide',
					'fade',
					'cube',
					'coverflow',
					'flip'
				)
			),
			'centeredSlides' => array(
				'type' => 'boolean',
				'default' => false
			),
			'breakpoints' => array(
				'type' => 'object',
				'default' => array(
					'640' => array(
						'slidesPerView' => 1
					),
					'768' => array(
						'slidesPerView' => 2
					),
					'1024' => array(
						'slidesPerView' => 3
					)
				)
			),
			'enableBreakpoints' => array(
				'type' => 'boolean',
				'default' => false
			),
			'customParams' => array(
				'type' => 'string',
				'default' => '{}'
			)
		),
		'textdomain' => 'query-loop-slider',
		'editorScript' => 'file:./index.js'
	)
);
