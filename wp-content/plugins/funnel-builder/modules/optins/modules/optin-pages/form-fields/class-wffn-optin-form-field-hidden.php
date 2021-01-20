<?php
defined( 'ABSPATH' ) || exit; //Exit if accessed directly

/**
 * This class will control all Optin Text mapping functionality on optin submission.
 * Class WFFN_Optin_Form_Field_Text
 */
class WFFN_Optin_Form_Field_Hidden extends WFFN_Optin_Form_Field {

	private static $ins = null;
	public static $slug = 'hidden';
	public $is_custom_field = true;
	public $index = 700;

	/**
	 * WFFN_Optin_Form_Field_Text constructor.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * @return WFFN_Optin_Form_Field_Hidden|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	/**
	 * @return string
	 */
	public static function get_slug() {
		return self::$slug;
	}

	/**
	 * Return title of this form field
	 */
	public function get_title() {
		return __( 'Hidden', 'funnel-builder' );
	}


	/**
	 * @param $field_data
	 *
	 * @return string|void
	 */
	public function get_field_output( $field_data ) {
		$field_data = wp_parse_args( $field_data, $this->get_field_format() );
		?>
        <input value="<?php echo esc_attr( $this->get_default_value( $field_data ) ); ?>" type="hidden" name="<?php echo esc_attr( $field_data['InputName'] ); ?>"/>
		<?php
	}

	/**
	 * @return array
	 */
	public function get_field_format() {
		return array(
			'type'     => $this::get_slug(),
			'label'    => __( 'Select', 'funnel-builder' ),
			'required' => false,
			'default'  => '',
		);
	}

	public function get_field_editor_html( $mode = 'new' ) {
		?>
        <div class="wfop_<?php echo esc_attr( $mode ); ?>_fields_wrap" data-type="<?php echo esc_attr( $this::get_slug() ); ?>">
            <div class="wffn_row_billing">
                <div class="wffn_billing_left">
                    <label><?php esc_html_e( 'Label', 'funnel-builder' ); ?></label>
                </div>
                <div class="wffn_billing_right">
                    <input type="text" onkeyup="window.wfop_design.fieldEdited('<?php echo esc_attr( $mode ) ?>',this.value, 'label','<# print(data.index); #>')" value="<# print(data.field.label); #>" class="form-control wffn_label">
                </div>
            </div>
            <div class="wffn_row_billing">
                <div class="wffn_billing_left">
                    <label for=""><?php esc_html_e( 'Default', 'funnel-builder' ); ?></label>
                </div>
                <div class="wffn_billing_right">
                    <input onkeyup="window.wfop_design.fieldEdited('<?php echo esc_attr( $mode ) ?>',this.value, 'default','<# print(data.index); #>')" type="text" value="<# print(data.field.default); #>" class="form-control">
                </div>
            </div>
        </div>
		<?php
	}
}

if ( class_exists( 'WFOPP_Core' ) ) {
	WFOPP_Core()->form_fields->register( WFFN_Optin_Form_Field_Hidden::get_instance() );
}
