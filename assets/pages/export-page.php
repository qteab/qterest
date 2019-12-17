<?php
	// check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
	return;
}
?>

<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	<p>Press the button below to export all contact requests</p>
	<form method="POST">
		<?php submit_button( 'Export' ); ?>
	</form>
</div>
