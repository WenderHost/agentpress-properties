<?php
/**
 * This widget presents loop content, based on your input, specifically for the homepage.
 *
 * @package AgentPress
 * @since 2.0
 * @author Nathan Rice
 */
class AgentPress_Featured_Properties_Widget extends WP_Widget {

	function AgentPress_Featured_Properties_Widget() {
		$widget_ops = array( 'classname' => 'featured-properties', 'description' => __( 'Display grid-style featured properties', 'agentpress-properties' ) );
		$control_ops = array( 'width' => 300, 'height' => 350 );
		$this->WP_Widget( 'featured-properties', __( 'AgentPress - Featured properties', 'agentpress-properties' ), $widget_ops, $control_ops );
	}

	function widget( $args, $instance ) {

		/** defaults */
		$instance = wp_parse_args( $instance, array(
			'title'          => '',
			'posts_per_page' => 10
		) );

		extract( $args );

		echo $before_widget;

			if ( ! empty( $instance['title'] ) ) {
				echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
			}

			$toggle = ''; /** for left/right class */

			$query_args = array(
				'post_type'      => 'property',
				'posts_per_page' => $instance['posts_per_page'],
				'paged'          => get_query_var('paged') ? get_query_var('paged') : 1
			);

			query_posts( $query_args );
			if ( have_posts() ) : while ( have_posts() ) : the_post();

				//* initialze the $loop variable
				$loop        = '';

				//* Pull all the property information
				$custom_text = genesis_get_custom_field( '_property_text' );
				$price       = genesis_get_custom_field( '_property_price' );
				$address     = genesis_get_custom_field( '_property_address' );
				$city        = genesis_get_custom_field( '_property_city' );
				$state       = genesis_get_custom_field( '_property_state' );
				$zip         = genesis_get_custom_field( '_property_zip' );

				$loop .= sprintf( '<a href="%s">%s</a>', get_permalink(), genesis_get_image( array( 'size' => 'properties' ) ) );

				if ( $price ) {
					$loop .= sprintf( '<span class="property-price">%s</span>', $price );
				}

				if ( strlen( $custom_text ) ) {
					$loop .= sprintf( '<span class="property-text">%s</span>', esc_html( $custom_text ) );
				}

				if ( $address ) {
					$loop .= sprintf( '<span class="property-address">%s</span>', $address );
				}

				if ( $city || $state || $zip ) {

					//* count number of completed fields
					$pass = count( array_filter( array( $city, $state, $zip ) ) );

					//* If only 1 field filled out, no comma
					if ( 1 == $pass ) {
						$city_state_zip = $city . $state . $zip;
					}
					//* If city filled out, comma after city
					elseif ( $city ) {
						$city_state_zip = $city . ", " . $state . " " . $zip;
					}
					//* Otherwise, comma after state
					else {
						$city_state_zip = $city . " " . $state . ", " . $zip;
					}

					$loop .= sprintf( '<span class="property-city-state-zip">%s</span>', trim( $city_state_zip ) );

				}

				$loop .= sprintf( '<a href="%s" class="more-link">%s</a>', get_permalink(), __( 'View Property', 'agentpress-properties' ) );

				$toggle = $toggle == 'left' ? 'right' : 'left';

				/** wrap in post class div, and output **/
				printf( '<div class="%s"><div class="widget-wrap"><div class="property-wrap">%s</div></div></div>', join( ' ', get_post_class( $toggle ) ), apply_filters( 'agentpress_featured_properties_widget_loop', $loop ) );

			endwhile; endif;
			wp_reset_query();

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {

		$instance = wp_parse_args( $instance, array(
			'title'          => '',
			'posts_per_page' => 10
		) );

		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id('title'), __( 'Title:', 'agentpress-properties' ), $this->get_field_id('title'), $this->get_field_name('title'), esc_attr( $instance['title'] ), 'width: 95%;' );

		printf( '<p>%s <input type="text" name="%s" value="%s" size="3" /></p>', __( 'How many results should be returned?', 'agentpress-properties' ), $this->get_field_name('posts_per_page'), esc_attr( $instance['posts_per_page'] ) );

	}
}