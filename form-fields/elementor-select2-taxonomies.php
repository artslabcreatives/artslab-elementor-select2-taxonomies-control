<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
use Elementor\Icons_Manager;
/**
 * Elementor Form Field - Select2 Field
 *
 * Add a new "Select2 Field" field to Elementor form widget.
 *
 * @since 1.0.0
 */
class Elementor_Select2_Taxonomies_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {

	/**
	 * Get field type.
	 *
	 * Retrieve select2 field unique ID.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Field type.
	 */
	public function get_type() {
		return 'select2-taxonomies-form';
	}

	/**
	 * Get field name.
	 *
	 * Retrieve select2 field label.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Field name.
	 */
	public function get_name() {
		return esc_html__( 'Elementor Select2 Taxonomies', 'elementor-form-select2-taxonomies-field' );
	}

	/**
	 * Render field output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param mixed $item
	 * @param mixed $item_index
	 * @param mixed $form
	 * @return void
	 */
	public function render( $item, $item_index, $form ) {
		$form_id = $form->get_id();
		$attr = [				
			'name' => 'form_fields['.$item['custom_id'].']',
			'class' => [
				'elementor-field-textual',
				'select2',
				'elementor-size-' . $item['input_size'],
			],
			'for' => $form_id . $item_index,
			'type' => 'select',
			'inputmode' => 'numeric',
		];

		if($item['select2-multiline-options'] == 'multiple'){
			$attr = [
				'name' => 'form_fields['.$item['custom_id'].']',
				'class' => [
					'elementor-field-textual',
					'select2',
					'elementor-size-' . $item['input_size'],
				],
				'for' => $form_id . $item_index,
				'type' => 'select',
				'inputmode' => 'numeric',
				'multiple' => 'multiple',
			];
		}

		$form->add_render_attribute(
			'select' . $item_index,
			$attr 
		);

		$options = get_terms($item['select2-taxonomies-options']);

		if ( ! $options ) {
			return '';
		}

		$op = "";

		foreach ($options as $key => $option) {
			$op .= "<option value=".$option->term_id.">".$option->name."</option>";
		}

		$e = '<select ' . $form->get_render_attribute_string( 'select' . $item_index ) . '>';
				$e .= $op;
		$e .= "</select>";
		echo $e;
	}

	/**
	 * Field validation.
	 *
	 * Validate select2 field value to ensure it complies to certain rules.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Field_Base   $field
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record  $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 * @return void
	 */
	public function validation( $field, $record, $ajax_handler ) {
		if ( empty( $field['value'] ) ) {
			return;
		}
	}

	/**
	 * Update form widget controls.
	 *
	 * Add input fields to allow the user to customize the select2 field.
	 *
	 * @since 1.0.0
	 * @access public
	 * @param \Elementor\Widget_Base $widget The form widget instance.
	 * @return void
	 */
	public function update_controls( $widget ) {
		$elementor = \ElementorPro\Plugin::elementor();

		$control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');

		if (is_wp_error($control_data)) {
			return;
		}
		$taxonomies = get_taxonomies();

		$field_controls = [
			'select2-multiline-options' => [
				'name' => 'select2-multiline-options',
				'label' => esc_html__('Select Multiple', 'elementor-form-select2-multiline-field'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'single',
				'dynamic' => [
					'active' => true,
				],
				'options' => [
					"multiple" => "Multiple",
					"single" => "Single",
				],
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'select2-taxonomies-options' => [
				'name' => 'select2-taxonomies-options',
				'label' => esc_html__('Taxonomy', 'elementor-form-select2-options-field'),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'options' => $taxonomies,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
		];

		$control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);

		$widget->update_control('form_fields', $control_data);
	}

	/**
	 * Field constructor.
	 *
	 * Used to add a script to the Elementor editor preview.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		add_action('elementor/frontend/after_register_scripts', [$this, 'select2formjs'], 15);
		add_action('elementor/frontend/after_enqueue_styles', [$this, 'select2formcss'], 15);

		add_action('elementor/preview/enqueue_scripts', [$this, 'select2formjs'], 15);
		add_action('elementor/preview/enqueue_styles', [$this, 'select2formcss'], 15);

		add_action('elementor/preview/init', [ $this, 'editor_preview_footer'], 16);
	}

	/**
	 * Elementor editor preview.
	 *
	 * Add a script to the footer of the editor preview screen.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function editor_preview_footer() {
		add_action( 'wp_footer', [ $this, 'content_template_script' ] );
	}

	/**
	 * Content template script.
	 *
	 * Add content template alternative, to display the field in Elemntor editor.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function content_template_script() {
		?>
		<script>
		jQuery( document ).ready( () => {

			elementor.hooks.addFilter(
				'elementor_pro/forms/content_template/field/<?php echo $this->get_type(); ?>',
				function ( inputField, item, i ) {
					const fieldType    = 'select';
					const fieldId      = `form_field_${i}`;
					const fieldClass   = `elementor-field-textual select2 elementor-field ${item.css_classes}`;
					const inputmode    = 'numeric';
					const pattern      = '[0-9\s]{19}';
					const options 	   = item['select2-taxonomies-options'];
					const multiple 	   = item['select2-multiline-options'];

					var splitoptions = options;

					return `<select type="${fieldType}" id="${fieldId}" class="${fieldClass}" inputmode="${inputmode}" pattern="${pattern}">
							${splitoptions}
						</select>
					`;
				}, 10, 3
			);
		});
		</script>
		<?php
	}

	public function select2formjs() {
		wp_register_script('select2form', plugins_url( '../assets/js/select2.min.js', __FILE__), array('jquery'));
		wp_register_script('select2', plugins_url( '../assets/js/select2.js', __FILE__), array('jquery'));
		wp_enqueue_script('select2form');
		wp_enqueue_script('select2');
	}
	
	public function select2formcss()
	{
		// code...
		wp_register_style('select2formcss', plugins_url( '../assets/css/select2.min.css', __FILE__));
		wp_enqueue_style('select2formcss');
	}

}
