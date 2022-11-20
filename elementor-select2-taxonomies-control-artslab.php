<?php
/**
 * Plugin Name: Elementor Select2 Taxonomies Control
 * Description: Adds a custom select2 taxonomies control to the default elmentor form
 * Plugin URI:  https://plugins.artslabcreatives.com
 * Version:     1.0.11
 * Author:      Artslab Creatives
 * Author URI:  https://plugins.artslabcreatives.com
 * Text Domain: elementor-select2-taxonomies-control-artslab
 *
 * Elementor tested up to: 3.7.0
 * Elementor Pro tested up to: 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register `credit-card-number` field-type to Elementor form widget.
 *
 * @since 1.0.11
 * @param \ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar $form_fields_registrar
 * @return void
 */
function add_new_select2_taxonomies_field( $form_fields_registrar ) {

    require_once( __DIR__ . '/form-fields/elementor-select2-taxonomies.php' );

    $form_fields_registrar->register( new \Elementor_Select2_Taxonomies_Field() );

}
add_action( 'elementor_pro/forms/fields/register', 'add_new_select2_taxonomies_field' );

require_once( __DIR__ . '/updater.php' );
new ALCES2TUpdater();