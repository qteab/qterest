<?php
	// check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<p><?php _e( 'Press the button below to export all contact requests', 'qterest' ); ?></p>
	<form method="POST">
		<?php do_action( 'qterest_export_form' ); ?> 
		<?php submit_button( __( 'Export', 'qterest' ) ); ?>
	</form>
</div>
