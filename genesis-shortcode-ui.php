<?php # -*- coding: utf-8 -*-
/**
 * Main plugin file.
 * @package           Genesis Shortcode UI
 * @author            David Decker
 * @copyright         Copyright (c) 2016, David Decker - DECKERWEB
 * @license           GPL-2.0+
 * @link              http://deckerweb.de/twitter
 *
 * @wordpress-plugin
 * Plugin Name:       Genesis Shortcode UI
 * Plugin URI:        https://github.com/deckerweb/genesis-shortcode-ui
 * Description:       Enhance the default Shortcodes of the Genesis Framework with a Shortcode UI powered by the Shortcake plugin.
 * Version:           2016.08.22
 * Author:            David Decker - DECKERWEB
 * Author URI:        http://deckerweb.de/
 * License:           GPL-2.0+
 * License URI:       http://www.opensource.org/licenses/gpl-license.php
 * Text Domain:       genesis-shortcode-ui
 * Domain Path:       /languages/
 * GitHub Plugin URI: https://github.com/deckerweb/genesis-shortcode-ui
 * GitHub Branch:     master
 *
 * Copyright (c) 2016 David Decker - DECKERWEB
 */

/**
 * Exit if called directly.
 */
if ( ! defined( 'WPINC' ) ) {

	die;

}  // end if


/**
 * Set filter for plugin's languages directory.
 *
 * @since  2016.08.17
 *
 * @return string Path to plugin's languages directory.
 */
function ddw_gsui_plugin_lang_dir() {

	return apply_filters(
		'gsui_filter_lang_dir',
		trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . 'languages'
	);

}  // end function


add_action( 'init', 'ddw_gsui_load_translations', 1 );
/**
 * Load the text domain for translation of the plugin.
 *
 * @since 2016.08.17
 *
 * @uses  load_textdomain()	To load translations first from WP_LANG_DIR sub folder.
 * @uses  load_plugin_textdomain() To additionally load default translations from plugin folder (default).
 */
function ddw_gsui_load_translations() {

	/** Set unique textdomain string */
	$gsui_textdomain = 'genesis-shortcode-ui';

	/** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
	$locale = apply_filters( 'plugin_locale', get_locale(), $gsui_textdomain );

	/** Set filter for WordPress languages directory */
	$gsui_wp_lang_dir = apply_filters(
		'gsui_filter_wp_lang_dir',
		trailingslashit( WP_LANG_DIR ) . 'plugins/' . $gsui_textdomain . '-' . $locale . '.mo'
	);

	/** Translations: First, look in WordPress' "languages" folder = custom & update-secure! */
	load_textdomain(
		$gsui_textdomain,
		$gsui_wp_lang_dir
	);

	/** Translations: Secondly, look in plugin's "languages" folder = default */
	load_plugin_textdomain(
		$gsui_textdomain,
		FALSE,
		//trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . 'languages'
		esc_attr( ddw_gsui_plugin_lang_dir() )
	);

}  // end function


register_activation_hook( __FILE__, 'ddw_gsui_activation_check' );
/**
 * Checks for activated Genesis Framework before allowing plugin to activate.
 *
 * @since 2016.08.17
 *
 * @uses  load_plugin_textdomain() To load default translations from plugin folder.
 * @uses  ddw_gfpe_plugin_lang_dir() To get filterable path to plugin's languages directory.
 * @uses  get_template_directory() To determine parent theme (Genesis).
 * @uses  deactivate_plugins() In case, deactivate ourself.
 * @uses  wp_die() In case, deactivate ourself, output user message.
 *
 * @param string $gfpe_deactivation_message
 */
function ddw_gsui_activation_check() {

	/** Load translations to display for the activation message. */
	load_plugin_textdomain(
		'genesis-shortcode-ui',
		FALSE,
		esc_attr( ddw_gsui_plugin_lang_dir() )
	);

	/** Check for activated Genesis Framework (= template/parent theme) */
	if ( ! function_exists( 'genesis_html5' ) && basename( get_template_directory() ) != 'genesis' ) {

		/** If no Genesis, deactivate ourself */
		deactivate_plugins( plugin_basename( __FILE__ ) );

		/** Message: no Genesis active */
		$gfpe_deactivation_message = sprintf(
			__( 'Sorry, you cannot activate the %1$s plugin unless you have installed the latest version of the %2$sGenesis Framework%3$s.', 'genesis-shortcode-ui' ),
			__( 'Genesis Shortcode UI', 'genesis-shortcode-ui' ),
			'<a href="http://deckerweb.de/go/genesis/" target="_new"><strong><em>',
			'</em></strong></a>'
		);

		/** Deactivation message */
		wp_die(
			$gfpe_deactivation_message,
			__( 'Plugin', 'genesis-shortcode-ui' ) . ': ' . __( 'Genesis Shortcode UI', 'genesis-shortcode-ui' ),
			array( 'back_link' => true )
		);

	}  // end if Genesis check

}  // end function


add_action( 'init', 'ddw_gsui_prepare_shortcode_ui' );
/**
 * If plugin Shortcake is active, load our support for it.
 *
 * @since 2016.08.17
 */
function ddw_gsui_prepare_shortcode_ui() {

	/** Check if Shortcake exists */
    if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) && ! function_exists( 'genesis_html5' ) ) {

        return;

    }  // end if

    /** Only create the UI when in the admin */
    if ( ! is_admin() ) {

        return;

    }  // end if

    /** Load Shortcodes UI registering */
	add_action( 'register_shortcode_ui', 'ddw_gsui_register_shortcodes_for_ui' );

}  // end function


/**
 * Set "Genesis" label for reuse.
 *
 * @since  2016.08.18
 *
 * @return string Label for "Genesis"
 */
function ddw_gsui_genesis_label() {

	$string_genesis = apply_filters(
		'gsui_filter_genesis_label',
		esc_html( 'Genesis', 'genesis-shortcode-ui' ) . ': '
	);

	return $string_genesis;

}  // end function


/**
 * Set string/path for Genesis logo for reuse.
 *
 * @since  2016.08.18
 *
 * @param  string $type Type of logo image/ icon to use
 * @param  string $dashicon Class/ string for a certain Dashicon font icon
 * @param  string $custom_path URL to custom image.
 *
 * @return string String/path of Genesis logo/ icon/ custom image.
 */
function ddw_gsui_genesis_logo( $type = 'default', $dashicon = '', $custom_path = '' ) {

	$type = strtolower( esc_attr( $type ) );

	$string_genesis_logo = '';

	if ( in_array( $type, array( 'footer', 'post', 'default' ) ) ) {

		$string_genesis_logo = apply_filters(
			'gsui_filter_genesis_logo_image',
			'<img src="' . plugins_url( 'genesis-shortcode-ui/images/genesis-logo-' . $type . '.png', dirname( __FILE__ ) ) . '" />'
		);

	} elseif ( 'dashicon' === $type ) {

		$string_genesis_logo = strtolower( esc_attr( $dashicon ) );

	} elseif ( 'custom' === $type ) {

		$string_genesis_logo = '<img src="' . esc_url( $custom_path ) . '" />';

	}  // end if

	/** Return filterable output: image path or Dashicon class */
	return apply_filters(
		'gsui_filter_genesis_logo',
		$string_genesis_logo
	);

}  // end function


/**
 * String helper for label "before".
 *
 * @since  2016.08.19
 *
 * @param  string $string String of element for Shortcode attribute
 * @param  string $position Position of the string
 *
 * @return string Label for the before string.
 */
function ddw_gsui_string_helper( $string = '', $position = 'before' ) {

	$position_string = '';
	$position        = strtolower( esc_attr( $position ) );

	if ( 'before' === $position ) {

		/* translators: means position before */
		$position_string = esc_html__( 'before', 'genesis-shortcode-ui' );

	} elseif ( 'after' === $position ) {

		/* translators: means position after */
		$position_string = esc_html__( 'after', 'genesis-shortcode-ui' );

	}  // end if

	$output = sprintf(
		/* translators: 1 = position before/after, 2 = the actual attribute from the Shortcode */
		esc_html__( 'Text/markup to place %1$s the %2$s', 'genesis-shortcode-ui' ),
		'<strong>' . $position_string . '</strong>',
		esc_html( $string )
	);

	return $output;

}  // end function


/**
 * Array of all supported default Genesis Shortcodes with all parameters
 *  necessary for the Shortcake plugin.
 *
 * @since  2016.08.18
 *
 * @uses   ddw_gsui_genesis_label()
 * @uses   ddw_gsui_genesis_logo()
 * @uses   ddw_gsui_string_helper()
 *
 * @return array Filterable array of all Shortcode args.
 */
function ddw_gsui_shortcode_tags() {

	/* translators: Attribute name (title) */
	$string_before         = esc_html__( 'Before', 'genesis-shortcode-ui' );
	/* translators: Attribute name (title) */
	$string_after          = esc_html__( 'After', 'genesis-shortcode-ui' );
	$string_relative_depth = sprintf(
		esc_html__( 'How many date/ time segments are shown if using the value %1$s for the date format attribute %2$s', 'genesis-shortcode-ui' ) . ' <a href="http://www.billerickson.net/genesis-relative-date-length/" target="_blank">' . esc_html__( 'More info on this', 'genesis-shortcode-ui' ) . '</a>',
		'<code>relative</code>',
		'(<code>format="relative"</code>)'
	);

	/** All needed args for the Shortcake plugin in one big array :) */
	$genesis_shortcodes = array(

		/** Footer Copyright */
		'sp_footer_copyright' => array(
			'tag'           => 'footer_copyright',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Footer Copyright', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'footer' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Copyright', 'genesis-shortcode-ui' ),
					'attr'  => 'copyright',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '©',	// '&#x000A9;'
					),
					'description' => esc_html__( 'Copyright symbol', 'genesis-shortcode-ui' ),
				),
				array(
					/* translators: Attribute name (title) */
					'label' => esc_html__( 'First', 'genesis-shortcode-ui' ),
					'attr'  => 'first',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => esc_html__( 'Text/markup to place between the copyright symbol and the copyright date', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'copyright', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'copyright', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Footer Child Theme Link */
		'sp_footer_childtheme_link' => array(
			'tag'           => 'footer_childtheme_link',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Footer Child Theme Link', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'footer' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '&#x000B7;',
					),
					'description' => ddw_gsui_string_helper( __( 'Child Theme link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'Child Theme link', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Footer Genesis Link */
		'sp_footer_genesis_link' => array(
			'tag'           => 'footer_genesis_link',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Footer Genesis Link', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'footer' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'URL', 'genesis-shortcode-ui' ),
					'attr'  => 'url',
					'type'  => 'url',
					'meta'  => array(
						'placeholder' => 'http://my.studiopress.com/themes/genesis/',
					),
					'description' => esc_html__( 'URL for Genesis Framework', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '&#x000B7;',
					),
					'description' => ddw_gsui_string_helper( __( 'Genesis Theme link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'Genesis Theme link', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Footer StudioPress Link */
		'sp_footer_studiopress_link' => array(
			'tag'           => 'footer_studiopress_link',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Footer StudioPress Link', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'footer' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						/* translators: Shortcode "StudioPress Link" default value/ placeholder for "before" attribute */
						'placeholder' => esc_html__( 'by', 'genesis-shortcode-ui' ),
					),
					'description' => ddw_gsui_string_helper( __( 'StudioPress link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'StudioPress link', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Footer WordPress Link */
		'sp_footer_wordpress_link' => array(
			'tag'           => 'footer_wordpress_link',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Footer WordPress Link', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'footer' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'WordPress link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'WordPress link', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Footer Site Title */
		'sp_footer_site_title' => array(
			'tag'           => 'footer_site_title',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Footer Site Title', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'default' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'Site Title', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'Site Title', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Footer Home Link */
		'sp_footer_home_link' => array(
			'tag'           => 'footer_home_link',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Footer Home Link', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'default' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'Home link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'Home link', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Footer Login Logout Link */
		'sp_footer_loginout_link' => array(
			'tag'           => 'footer_loginout_link',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Footer Log in/out Link', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'default' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'Log in/ out link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'Log in/ out link', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Date */
		'sp_post_date' => array(
			'tag'           => 'post_date',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Date', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Format', 'genesis-shortcode-ui' ),
					'attr'  => 'format',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => get_option( 'date_format' ),
					),
					'description' => esc_html__( 'The format for the date. Defaults to the date format configured in your WordPress options.', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => esc_html__( 'Relative Depth (date segments)', 'genesis-shortcode-ui' ),
					'attr'  => 'relative_depth',
					'type'  => 'number',
					'meta'  => array(
						'placeholder' => 2,
					),
					'description' => $string_relative_depth,
				),
				array(
					'label' => esc_html__( 'Label', 'genesis-shortcode-ui' ),
					'attr'  => 'label',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => esc_html__( 'Text to place before the post date', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post date', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post date', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Time */
		'sp_post_time' => array(
			'tag'           => 'post_time',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Time', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Format', 'genesis-shortcode-ui' ),
					'attr'  => 'format',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => get_option( 'time_format' ),
					),
					'description' => esc_html__( 'The format for the time. Defaults to the time format configured in your WordPress options.', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => esc_html__( 'Label', 'genesis-shortcode-ui' ),
					'attr'  => 'label',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => esc_html__( 'Text to place before the post time', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post time', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post time', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Modified Date */
		'sp_post_modified_date' => array(
			'tag'           => 'post_modified_date',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Last Modified Date', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Format', 'genesis-shortcode-ui' ),
					'attr'  => 'format',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => get_option( 'date_format' ),
					),
					'description' => esc_html__( 'The format for the last modified date. Defaults to the date format configured in your WordPress options.', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => esc_html__( 'Relative Depth (date segments)', 'genesis-shortcode-ui' ),
					'attr'  => 'relative_depth',
					'type'  => 'number',
					'meta'  => array(
						'placeholder' => 2,
					),
					'description' => $string_relative_depth,
				),
				array(
					'label' => esc_html__( 'Label', 'genesis-shortcode-ui' ),
					'attr'  => 'label',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => esc_html__( 'Text to place before the post last modified date', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post last modified date', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post last modified date', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Modified Time */
		'sp_post_modified_time' => array(
			'tag'           => 'post_modified_time',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Last Modified Time', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Format', 'genesis-shortcode-ui' ),
					'attr'  => 'format',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => get_option( 'time_format' ),
					),
					'description' => esc_html__( 'The format for the last modified time. Defaults to the time format configured in your WordPress options.', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => esc_html__( 'Label', 'genesis-shortcode-ui' ),
					'attr'  => 'label',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => esc_html__( 'Text to place before the post last modified time', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post last modified time', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post last modified time', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Author */
		'sp_post_author' => array(
			'tag'           => 'post_author',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Author', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post author name', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post author name', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Author Link */
		'sp_post_author_link' => array(
			'tag'           => 'post_author_link',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Author Link', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post author link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post author name', 'genesis-shortcode-ui' ), 'before' ),
				),
			),
		),

		/** Post Author Posts Link */
		'sp_post_author_posts_link' => array(
			'tag'           => 'post_author_posts_link',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Author Posts Link', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post author posts link (archive)', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post author posts link (archive)', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Comments */
		'sp_post_comments' => array(
			'tag'           => 'post_comments',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Comments', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Zero Comments', 'genesis-shortcode-ui' ),
					'attr'  => 'zero',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( 'Leave a Comment', 'genesis-shortcode-ui' ),
					),
					'description' => esc_html__( 'Text to display if zero comments on the post', 'genesis-shortcode-ui' ),
				),

				array(
					'label' => esc_html__( 'One Comment', 'genesis-shortcode-ui' ),
					'attr'  => 'one',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( '1 Comment', 'genesis-shortcode-ui' ),
					),
					'description' => esc_html__( 'Text to display if one comment on the post', 'genesis-shortcode-ui' ),
				),

				array(
					'label' => esc_html__( 'More Comments', 'genesis-shortcode-ui' ),
					'attr'  => 'more',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( '% Comments', 'genesis-shortcode-ui' ),
					),
					'description' => esc_html__( 'Text to display if more than one comment on the post', 'genesis-shortcode-ui' ),
				),

				array(
					'label'   => esc_html__( 'Hide if off', 'genesis-shortcode-ui' ),
					'attr'    => 'hide_if_off',
					'type'    => 'select',
					'options' => array(
						'enabled'  => esc_html__( 'enabled', 'shortcode-item-updated' ),
						'disabled' => esc_html__( 'disabled', 'shortcode-item-updated' ),
					),
					'description' => esc_html__( 'Enable the comment link even if comments are off', 'genesis-shortcode-ui' ),
				),

				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post comment link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post comment link', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Tags */
		'sp_post_tags' => array(
			'tag'           => 'post_tags',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Tags', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Separator', 'genesis-shortcode-ui' ),
					'attr'  => 'sep',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => ', ',
					),
					'description' => esc_html__( 'Separator between post tags', 'genesis-shortcode-ui' ),
				),

				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( 'Tagged With: ', 'genesis-shortcode-ui' ),
					),
					'description' => ddw_gsui_string_helper( __( 'tag list', 'genesis-shortcode-ui' ), 'before' ) . ' ' . esc_html__( 'Default "Tagged With: "', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'tag list', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Categories */
		'sp_post_categories' => array(
			'tag'           => 'post_categories',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Categories', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Separator', 'genesis-shortcode-ui' ),
					'attr'  => 'sep',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => ', ',
					),
					'description' => esc_html__( 'Separator between post categories', 'genesis-shortcode-ui' ),
				),

				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( 'Filed Under: ', 'genesis-shortcode-ui' ),
					),
					'description' => ddw_gsui_string_helper( __( 'post category list', 'genesis-shortcode-ui' ), 'before' ) . ' ' . esc_html__( 'Default "Filed Under: "', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post category list', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Terms */
		'sp_post_terms' => array(
			'tag'           => 'post_terms',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Terms', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Taxonomy', 'genesis-shortcode-ui' ),
					'attr'  => 'taxonomy',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => 'category',
					),
					'description' => esc_html__( 'Which taxonomy to use. Default "category"', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => esc_html__( 'Separator', 'genesis-shortcode-ui' ),
					'attr'  => 'sep',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => ', ',
					),
					'description' => esc_html__( 'Separator between the terms', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( 'Tagged With: ', 'genesis-shortcode-ui' ),
					),
					'description' => ddw_gsui_string_helper( __( 'post terms list', 'genesis-shortcode-ui' ), 'before' ) . ' ' . esc_html__( 'Default "Filed Under: "', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'post terms list', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

		/** Post Edit */
		'sp_post_edit' => array(
			'tag'           => 'post_edit',
			/* translators: Attribute name (title) */
			'label'         => ddw_gsui_genesis_label() . esc_html__( 'Post Edit', 'genesis-shortcode-ui' ),
			'listItemImage' => ddw_gsui_genesis_logo( 'post' ),
			'attrs'         => array(
				array(
					'label' => esc_html__( 'Link', 'genesis-shortcode-ui' ),
					'attr'  => 'link',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => esc_html__( '(Edit)', 'genesis-shortcode-ui' ),
					),
					'description' => esc_html__( 'Text for edit link. Default "(Edit)"', 'genesis-shortcode-ui' ),
				),
				array(
					'label' => $string_before,
					'attr'  => 'before',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'edit post link', 'genesis-shortcode-ui' ), 'before' ),
				),
				array(
					'label' => $string_after,
					'attr'  => 'after',
					'type'  => 'text',
					'meta'  => array(
						'placeholder' => '',
					),
					'description' => ddw_gsui_string_helper( __( 'edit post link', 'genesis-shortcode-ui' ), 'after' ),
				),
			),
		),

	);  // end array

	/** Return the array */
	return apply_filters(
		'gsui_filter_genesis_shortcodes_ui_args',
		$genesis_shortcodes
	);

}  // end function


/**
 * Shortcode UI setup for Shortcake plugin (Shortcode UI).
 * @link   https://wordpress.org/plugins/shortcode-ui/
 *
 * @since  2016.08.17
 *
 * @uses   ddw_gsui_shortcode_tags()
 * @uses   shortcode_ui_register_for_shortcode()
 *
 * @return array Array with Shortcode UI arguments.
 */
function ddw_gsui_register_shortcodes_for_ui() {

	/** Collect the Shortcode tags and args callback arrays */
	$shortcode_tags = (array) ddw_gsui_shortcode_tags();

	/** Pass the Shortcodes and UI args to Shortcake plugin - filterable */
	foreach ( $shortcode_tags as $shortcode_id => $shortcode_tag ) {

		/**
		 * We need the following condition to make our filter 'gsui_filter_genesis_shortcodes_ui_args'
		 *  work to exclude one or more Shortcodes from passing to the UI.
		 */
		if ( ! empty( $shortcode_tag ) ) {

			shortcode_ui_register_for_shortcode(
				$shortcode_tag[ 'tag' ],
				apply_filters(
					"gsui_filter_shortcode_ui_args_{$shortcode_tag[ 'tag' ]}",
					(array) $shortcode_tag
				)
			);

		}  // end if

	}  // end foreach

}  // end function
