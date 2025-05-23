<?php

/**
 * @var \WPDesk\Forms\Field $field
 * @var string $name_prefix
 * @var string $value
 */

$add_array = ( isset( $multiple ) && true === $multiple || $field->is_multiple() );
?>
<select
	id="<?php echo \esc_attr( $field->get_id() ); ?>"
	<?php
	if ( $field->has_classes() ) {
		?>
	class="<?php echo \esc_attr( $field->get_classes() ); ?>"
		<?php
	}
	?>
	name="<?php echo \esc_attr( $name_prefix ) . '[' . \esc_attr( $field->get_name() ) . ']' . ( true === $add_array ? '[]' : '' ); ?>"
	<?php
	foreach ( $field->get_attributes() as $key => $attr_val ) {
		echo \esc_attr( $key );
		?>
	="<?php echo \esc_attr( $attr_val ); ?>"
		<?php
	}
	?>

	<?php
	if ( $field->is_required() ) {
		?>
	required="required"
		<?php
	}
	?>
	<?php
	if ( $field->is_disabled() ) {
		?>
	disabled="disabled"
		<?php
	}
	?>
	<?php
	if ( $field->is_readonly() ) {
		?>
	readonly="readonly"
		<?php
	}
	?>
	<?php
	if ( $field->is_multiple() ) {
		?>
	multiple="multiple"
		<?php
	}
	?>
>
	<?php
	if ( $field->has_placeholder() ) {
		?>
	<option value="">
		<?php
		echo \esc_html( $field->get_placeholder() );
		?>
	</option>
		<?php
	}
	?>

	<?php
	foreach ( $field->get_possible_values() as $possible_value => $label ) {
		?>
		<option
			<?php
			if ( $possible_value === $value || ( is_array( $value ) && in_array( $possible_value, $value, false ) ) || \is_numeric( $possible_value ) && \is_numeric( $value ) && (int) $possible_value === (int) $value ) { // @phpstan-ignore-line
				?>
		selected="selected"
				<?php
			}

			if ( $field->get_name() === \DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Form\Fields\ImportOptionsFormFields::FIELD_REMOVED_PRODUCTS && $possible_value === \DropshippingXmlFreeVendor\WPDesk\Library\DropshippingXmlCore\Form\Fields\ImportOptionsFormFields::OPTION_NO_PRODUCT_TRASH ) {
				?>
	disabled 
				<?php
			}
			?>
			value="<?php echo \esc_attr( $possible_value ); ?>"><?php echo \esc_html( $label ); ?></option>
		<?php
	}
	?>
</select>
<?php
