<?php

class GF_Tooltips_Front
{

	/**
	 * This is our constructor
	 *
	 * @return GF_Tooltips
	 */
	public function __construct() {

		// GF specific
		add_action( 'gform_enqueue_scripts', array( $this, 'scripts_styles'      ), 10, 2 );
		add_action( 'gform_field_css_class', array( $this, 'set_tooltip_class'   ), 10, 3 );
		add_filter( 'gform_field_content',   array( $this, 'set_tooltip_display' ), 10, 5 );

	}

	/**
	 * [get_tooltip_data description]
	 * @param  boolean $key [description]
	 * @return [type]       [description]
	 */
	static function get_tooltip_data( $key = false, $default = '' ) {

		$data = get_option( 'gf-tooltips' );

		if ( ! $data ) {
			return false;
		}

		if ( ! $key ) {
			return $data;
		}

		// check settings and return choice or default
		$item = isset( $data[ $key ] ) && ! empty( $data[ $key ] ) ? $data[ $key ] : $default;

		return $item;

	}

	/**
	 * [set_field_class description]
	 * @param [type] $classes [description]
	 * @param [type] $field   [description]
	 * @param [type] $form    [description]
	 */

	public function set_tooltip_class( $classes, $field, $form ){

		// grab option field
		$data = get_option( 'gf-tooltips' );

		// bail if we have nothing
		if ( ! $data ) {
			return $classes;
		}

		if ( ! isset( $field['tooltipContent'] ) || isset( $field['tooltipContent'] ) && empty( $field['tooltipContent'] ) ) {
			// bail if no tooltip actually exists
			if ( ! isset( $field['tooltipTitle'] ) || isset( $field['tooltipTitle'] ) && empty( $field['tooltipTitle'] ) ) {
				return $classes;
			}
		}

		// add class for label tooltip
		if ( isset ( $data['style'] ) && $data['style'] == 'label' ) {
			$classes .= ' gf-tooltip gf-tooltip-label';
		}

		// add class for icon tooltip
		if ( isset ( $data['style'] ) && $data['style'] == 'icon' ) {
			$classes .= ' gf-tooltip gf-tooltip-icon';
		}

		// add class for icon tooltip
		if ( isset ( $data['style'] ) && $data['style'] == 'single' ) {
			$classes .= ' gf-tooltip gf-tooltip-single';
		}

		return $classes;
	}

	/**
	 * [set_tooltip_display description]
	 * @param [type] $content [description]
	 * @param [type] $field   [description]
	 * @param [type] $value   [description]
	 * @param [type] $lead_id [description]
	 * @param [type] $form_id [description]
	 */
	public function set_tooltip_display( $content, $field, $value, $lead_id, $form_id ) {

		// this is only for the front end
		if ( is_admin() ) {
			return $content;
		}

		// grab our tooltip style first
		$style = self::get_tooltip_data( 'style', 'icon' );

		// bail if we have no position set
		if ( ! $style ) {
			return $content;
		}

		if ( ! isset( $field['tooltipContent'] ) || isset( $field['tooltipContent'] ) && empty( $field['tooltipContent'] ) ) {
			// bail if no tooltip actually exists
			if ( ! isset( $field['tooltipTitle'] ) || isset( $field['tooltipTitle'] ) && empty( $field['tooltipTitle'] ) ) {
				return $content;
			}
		}

		// get our content and sanitize it
		$tooltip     = esc_attr( $field['tooltipTitle'] );
		$description = esc_attr( $field['tooltipContent'] );

		// build out label version
		if ( $style == 'label' ) {
			$content = self::render_tooltip_label( $content, $tooltip, $description );
		}

		// build out icon version
		if ( $style == 'icon' ) {
			$content = self::render_tooltip_icon( $content, $tooltip, $description );
		}

		// build out single version
		if ( $style == 'single' ) {
			$content = self::render_tooltip_single( $content, $tooltip, $description );
		}

		// return field content with new tooltip
		return $content;

	}

	/**
	 * filter the existing label markup to add the data attribute
	 * @param  [type] $content [description]
	 * @param  [type] $tooltip [description]
	 * @return [type]          [description]
	 */
	static function render_tooltip_label( $content, $tooltip = '', $description = '' ) {
		$attr_title     = ( empty($tooltip)     ? '' : ' title="' . $tooltip . '"' );
		$attr_content   = ( empty($description) ? '' : ' data-content="' . $tooltip . '"' );
		$attr_toggle    = ( empty($description) ? ' data-toggle="popover"' : ( empty($tooltip) ? '' : ' data-toggle="tooltip"' ) );
		$attr_placement = ' data-placement="' . self::get_tooltip_data( 'placement', 'auto' ) . '"';

		return GF_Tooltips::str_replace_limit( '<label', '<label data-trigger="click"' . $attr_toggle . $attr_placement . $attr_title . $attr_content, $content );

	}

	/**
	 * [render_tooltip_icon description]
	 * @param  [type] $content [description]
	 * @param  [type] $tooltip [description]
	 * @return [type]          [description]
	 */
	static function render_tooltip_icon( $content, $tooltip = '', $description = '' ) {
		$attr_title     = ( empty($tooltip)     ? '' : ' title="' . $tooltip . '"' );
		$attr_content   = ( empty($description) ? '' : ' data-content="' . $tooltip . '"' );
		$attr_toggle    = ( empty($description) ? ' data-toggle="popover"' : ( empty($tooltip) ? '' : ' data-toggle="tooltip"' ) );
		$attr_placement = ' data-placement="' . self::get_tooltip_data( 'placement', 'auto' ) . '"';

		$img  = GF_Tooltips::get_tooltip_icon_img( false );
		$icon = '<img src="'.esc_url( $img ).'" class="gf-tooltip-icon-img" data-trigger="click"' . $attr_toggle . $attr_placement . $attr_title . $attr_content . '>';

		// drop our tooltip on there
		return GF_Tooltips::str_replace_limit( '</label>', $icon . '</label>', $content );

	}

	/**
	 * [render_tooltip_single description]
	 * @param  [type] $content [description]
	 * @param  [type] $tooltip [description]
	 * @return [type]          [description]
	 */
	static function render_tooltip_single( $content, $tooltip = '', $description = '' ) {
		$attr_title     = ( empty($tooltip)     ? '' : ' title="' . $tooltip . '"' );
		$attr_content   = ( empty($description) ? '' : ' data-content="' . $tooltip . '"' );
		$attr_toggle    = ( empty($description) ? ' data-toggle="popover"' : ( empty($tooltip) ? '' : ' data-toggle="tooltip"' ) );
		$attr_placement = ' data-placement="' . self::get_tooltip_data( 'placement', 'auto' ) . '"';

		$img  = GF_Tooltips::get_tooltip_icon_img( false );
		$icon = '<span class="gf-tooltip-icon-wrap"><img src="'.esc_url( $img ).'" class="gf-tooltip-icon-img" data-trigger="click"' . $attr_toggle . $attr_placement . $attr_title . $attr_content . '"></span>';

		// drop our tooltip on there
		return GF_Tooltips::str_replace_limit( '</div>', '</div>' . $icon, $content );

	}

	/**
	 * [get_tooltip_customs description]
	 * @param  boolean $option [description]
	 * @return [type]          [description]
	 */
	static function get_tooltip_customs( $option = false ) {

		$showdelay = apply_filters( 'gf_tooltips_show_delay', 700 );
		$showsolo  = apply_filters( 'gf_tooltips_show_solo', true );

		$hidedelay = apply_filters( 'gf_tooltips_hide_delay', 300 );

		$data = array(
			'showdelay' => (int) $showdelay,
			'showsolo'  => (bool) $showsolo,
			'hidedelay' => (int) $hidedelay,
		);

		// return the whole array
		if ( ! $option ) {
			return $data;
		}

		// return the specified option
		return $data[ $option ];

	}

	/**
	 * [scripts_styles description]
	 * @return [type] [description]
	 */
	public function scripts_styles( $form, $is_ajax ) {

		if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) :
		// load non-minified version and debug script with cache breaking timestamp if set
			wp_enqueue_style(  'gf-tooltips',  plugins_url( '/css/gftips.front.css',    __FILE__ ), array(), GFT_VER, 'all' );
			wp_enqueue_script( 'bs-tooltip',   plugins_url( '/js/bootstrap.tooltip.js', __FILE__ ), array( 'jquery' ), time(), true );
			wp_enqueue_script( 'bs-popover',   plugins_url( '/js/bootstrap.popover.js', __FILE__ ), array( 'bs-tooltip' ), time(), true );
			wp_enqueue_script( 'gf-tooltips',  plugins_url( '/js/gftips.front.js',      __FILE__ ), array( 'bs-tooltip', 'bs-popover' ), GFT_VER, true );
		else:
		// load the normal minified
			wp_enqueue_style(  'gf-tooltips',  plugins_url( '/css/gftips.front.min.css', __FILE__ ), array(), GFT_VER, 'all' );
			wp_enqueue_script( 'gf-bootstrap', plugins_url( '/js/bootstrap.min.js',      __FILE__ ), array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'gf-tooltips',  plugins_url( '/js/gftips.front.min.js',   __FILE__ ), array( 'gf-bootstrap' ), GFT_VER, true );
		endif;

	}

/// end class
}

new GF_Tooltips_Front();
