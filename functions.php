<?php
function twentytenjump_setup() {
	
	// define constants for textdomain
	define( 'TWENTYTENJUMP', 'twentyten' );
	
	// load textdomain with path
	load_child_theme_textdomain( TWENTYTENJUMP, STYLESHEETPATH . '/languages' );
	
	remove_filter( 'excerpt_length', 'twentyten_excerpt_length' );
	add_filter( 'excerpt_length', 'twentytenjump_excerpt_length' );
	
	// unregister default header images
	unregister_default_headers( array(
		'berries',
		'cherryblossom',
		'concave',
		'fern',
		'forestfloor', // Waldboden
		'inkwell', // WordPress Image
		//'path' , // Allee
		'sunset'
	) );
	
	// unregister menu from Twenty Ten theme
	unregister_nav_menu( 'primary' );
	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'primary-menu'   => __( 'Primary Navigation', TWENTYTENJUMP ),
			'secondary-menu' => __( 'Secondary Navigation', TWENTYTENJUMP )
		)
	);
	
	add_action( 'widgets_init', 'register_limited_catagories_widget' );
}
add_action( 'after_setup_theme', 'twentytenjump_setup', 11 );


function twentytenjump_excerpt_length( $length ){
	
	return 10;
}


class limited_catagories_list_widget extends WP_Widget {
	
	function limited_catagories_list_widget(){
		$widget_ops = array( 'classname' => __('Selective Catagories', TWENTYTENJUMP ), 'description' => __( 'Show a list of Categories, with the ability to exclude categories', TWENTYTENJUMP ) );
		
		$control_ops = array( 'id_base' => 'some-cats-widget' );
		$this->WP_Widget( 'some-cats-widget', __( 'Selective Catagories', TWENTYTENJUMP ), $widget_ops, $control_ops );
	}
	
	function form ( $instance){
		$defaults = array( 'title' => __( 'Catagories', TWENTYTENJUMP ), 'cats' => '', 'count' => 0 );
		$instance = wp_parse_args( (array) $instance, $defaults );
		$count = isset($instance['count']) ? (bool) $instance['count'] :false;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', TWENTYTENJUMP ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>
		<p>
			<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>"<?php checked( $count ); ?> />
			<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e( 'Show post counts', TWENTYTENJUMP ); ?></label><br />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cats' ); ?>"><?php _e( 'Categories to exclude (comma separated list of Category-IDs): ', TWENTYTENJUMP ); ?></label>
			<input id="<?php echo $this->get_field_id( 'cats' ); ?>" name="<?php echo $this->get_field_name( 'cats' ); ?>" value="<?php echo $instance['cats']; ?>" class="widefat" />
		</p>
		<?php
	}
	
	function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			$instance['count'] = !empty($new_instance['count']) ? 1 : 0;
			$instance['cats']  = strip_tags( $new_instance['cats'] );
			return $instance;
	}
	
	function widget($args, $instance){
		extract( $args );
		$title = apply_filters('widget_title', $instance['title'] );
		$c     = $instance['count'] ? '1' : '0';
		$cats  = $instance['cats'];
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<ul>';
		wp_list_categories("exclude=$cats&title_li=&show_count=$c");
		echo '</ul>';
		echo $after_widget;
	}

}

function register_limited_catagories_widget(){
	register_widget('limited_catagories_list_widget');
}
?>