<?php
wp_nonce_field( 'agentpress_details_metabox_save', 'agentpress_details_metabox_nonce' );

echo '<div style="width: 90%; float: left">';

	printf( '<p><label>%s<input type="text" name="ap[_property_text]" value="%s" /></label></p>', __( 'Custom Text: ', 'agentpress-properties' ), esc_attr( genesis_get_custom_field('_property_text') ) );
	printf( '<p><span class="description">%s</span></p>', __( 'Custom text shows on the featured properties widget image.', 'agentpress-properties' ) );

echo '</div><br style="clear: both;" /><br /><br />';

$pattern = '<p><label>%s<br /><input type="text" name="ap[%s]" value="%s" /></label></p>';

echo '<div style="width: 45%; float: left">';

	foreach ( (array) $this->property_details['col1'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( genesis_get_custom_field( $key ) ) );
	}
	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_details]\')', __( 'Send to text editor', 'agentpress-properties' ) );

echo '</div>';

echo '<div style="width: 45%; float: left;">';

	foreach ( (array) $this->property_details['col2'] as $label => $key ) {
		printf( $pattern, esc_html( $label ), $key, esc_attr( genesis_get_custom_field( $key ) ) );
	}

echo '</div><br style="clear: both;" /><br /><br />';

echo '<div style="width: 45%; float: left;">';

	printf( __( '<p><label>Enter Map Embed Code:<br /><textarea name="ap[_property_map]" rows="5" cols="18" style="%s">%s</textarea></label></p>', 'agentpress-properties' ), 'width: 99%;', htmlentities( genesis_get_custom_field('_property_map') ) );

	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_map]\')', __( 'Send to text editor', 'agentpress-properties' ) );

echo '</div>';

echo '<div style="width: 45%; float: left;">';

	printf( __( '<p><label>Enter Video Embed Code:<br /><textarea name="ap[_property_video]" rows="5" cols="18" style="%s">%s</textarea></label></p>', 'agentpress-properties' ), 'width: 99%;', htmlentities( genesis_get_custom_field('_property_video') ) );

	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_video]\')', __( 'Send to text editor', 'agentpress-properties' ) );

echo '</div><br style="clear: both;" />';