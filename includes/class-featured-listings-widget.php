<?php
/**
 * This widget presents loop content, based on your input, specifically for the homepage.
 *
 * @package AgentPress
 * @since 2.0
 * @author Nathan Rice
 */
class AgentPress_Featured_Listings_Widget extends WP_Widget {

	function AgentPress_Featured_Listings_Widget() {
		$widget_ops = array( 'classname' => 'featured-listings', 'description' => __( 'Display grid-style featured listings', 'agentpress-listings' ) );
		$control_ops = array( 'width' => 300, 'height' => 350 );
		$this->WP_Widget( 'featured-listings', __( 'AgentPress - Featured Listings', 'agentpress-listings' ), $widget_ops, $control_ops );
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
				'post_type'      => 'listing',
				'posts_per_page' => $instance['posts_per_page'],
				'paged'          => get_query_var('paged') ? get_query_var('paged') : 1
			);

			query_posts( $query_args );
			if ( have_posts() ) : while ( have_posts() ) : the_post();

				$listing_text = genesis_get_custom_field( '_listing_text' );
				$name = get_the_title();
				if( function_exists( 'get_field' ) ){
					$map = get_field( 'map' );
					$address = $map['address'];
				} else {
					$address = '**Missing ACF Plugin**';
				}

				$sq_ft = genesis_get_custom_field( '_listing_sqft' );

				$loop = ''; // init

				$loop .= sprintf( '<a href="%s">%s</a>', get_permalink(), genesis_get_image( array( 'size' => 'properties' ) ) );

				$sq_ft = ( empty( $sq_ft ) )? $sq_ft = '-- TBA --' : $sq_ft . ' ft<sup>2</sup>' ;

				if( $sq_ft ) {
					$loop .= sprintf( '<span class="listing-price">%s</span>', $sq_ft );
				}

				if( empty( $listing_text) ){
					$terms = get_the_terms( $post->ID, 'location' );
					if( $terms )
						//echo '<pre>$terms = '.print_r( $terms, true ).'</pre>';
						if( ! is_wp_error( $terms ) && is_array( $terms ) && 0 < count( $terms ) )
							$listing_text = $terms[0]->name;
				}

				if( $listing_text ) {
					$loop .= sprintf( '<span class="listing-text">%s</span>', $listing_text );
				}

				if( $name && ! stristr( $address, $name ) )
					$loop .= sprintf( '<span class="listing-title"><a href="%s">%s</a></span>', get_permalink() , $name );

				if ( $address != $name ) {
					$formatted_address = $address;

					/*
					 * If the property's name == the 1st line of its address,
					 * link the first line of the address to the property's page,
					 * and add a double br to make the content the same height as
					 * properties with names != the 1st line of their addresses.
					 */
					if( stristr( $address, $name ) )
						$formatted_address = preg_replace( '/(.*),/U', '<a href="' . get_permalink() . '">${1}</a>,', $formatted_address, 1  ) . '<br /><br />';

					$formatted_address = preg_replace( '/,/', '<br \/>', $formatted_address, 1 );
					$formatted_address = str_replace( array( ', United States', ', US'), '', $formatted_address );
					$loop .= sprintf( '<span class="listing-address">%s</span>', $formatted_address );
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

					$loop .= sprintf( '<span class="listing-city-state-zip">%s</span>', trim( $city_state_zip ) );

				}

				if( $address == $name )
					$loop .= '<span class="listing-address">&nbsp;</span>';

				//$loop .= sprintf( '<a href="%s" class="more-link">%s</a>', get_permalink(), __( 'View Listing', 'agentpress' ) );

				$toggle = $toggle == 'left' ? 'right' : 'left';

				/** wrap in post class div, and output **/
				printf( '<div class="%s"><div class="widget-wrap"><div class="listing-wrap">%s</div></div></div>', join( ' ', get_post_class( $toggle ) ), apply_filters( 'agentpress_featured_listings_widget_loop', $loop ) );

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

		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id('title'), __( 'Title:', 'agentpress-listings' ), $this->get_field_id('title'), $this->get_field_name('title'), esc_attr( $instance['title'] ), 'width: 95%;' );

		printf( '<p>%s <input type="text" name="%s" value="%s" size="3" /></p>', __( 'How many results should be returned?', 'agentpress-listings' ), $this->get_field_name('posts_per_page'), esc_attr( $instance['posts_per_page'] ) );

	}
}