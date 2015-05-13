<?php 

add_action('widgets_init','mom_widget_weather');

function mom_widget_weather() {
	register_widget('mom_widget_weather');
	
	}

class mom_widget_weather extends WP_Widget {
	function mom_widget_weather() {
			
		$widget_ops = array('classname' => 'momizat-weather_widget','description' => __('Widget display weather widget','theme'));
		$this->WP_Widget('momizatWeatherWidget',__('Momizat - Weather','theme'),$widget_ops);

		}
		
	function widget( $args, $instance ) {
		extract( $args );
		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$city = $instance['city'];
		$units = $instance['units'];
		$lang = $instance['lang'];
		$display = $instance['display'];
		$search = $instance['search'];
		$days = $instance['days'];


		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
		echo $before_title . $title . $after_title;
                ?>
                                <?php if ($search == 'on') { ?>
				<form action="" method="post" class="weather-form">
                                    <input type="text" class="weather-city-name" placeholder="<?php _e('Enter City ...', 'theme'); ?>" data-lang="<?php echo $lang; ?>" data-units="<?php echo $units; ?>">
                                    <span class="sf-loading"><img src="<?php echo MOM_IMG; ?>/ajax-search-nav.gif" alt="loading..." width="16" height="16"></span>
                                </form>
				<?php } ?>
                <?php
                momizat_weather($city, $units, 'm/d/Y', $lang, $display, $days);
		
		/* After widget (defined by themes). */
		echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['city'] = $new_instance['city'];
		$instance['units'] = $new_instance['units'];
		$instance['lang'] = $new_instance['lang'];
		$instance['display'] = $new_instance['display'];
		$instance['search'] = $new_instance['search'];
		$instance['days'] = $new_instance['days'];
		delete_transient('mom_weather_data_'.$instance['city']);
		delete_transient('mom_daily_weather_data_'.$instance['city']);

		return $instance;
	}
	
function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
                        'title' => __('Weather','theme'), 
			'city' => 'Cairo',
			'units' => 'metric',
			'lang' => 'en',
			'display' => '',
			'search' => 'on',
			'days' => 6,
 			);
		$instance = wp_parse_args( (array) $instance, $defaults );
	
		?>
	
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:','theme'); ?></label>
		<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>"  class="widefat" />
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'city' ); ?>"><?php _e('City Name:','theme'); ?></label>
		<input type="text" id="<?php echo $this->get_field_id( 'city' ); ?>" name="<?php echo $this->get_field_name( 'city' ); ?>" value="<?php echo $instance['city']; ?>" class="widefat" />
		</p>

    	<p>
		<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e('Custom display title:', 'framework') ?></label>
		<input type="text" id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>" value="<?php echo $instance['display']; ?>"  class="widefat" />
	</p>

        <p>
		<label for="<?php echo $this->get_field_id( 'units' ); ?>"><?php _e('Units', 'framework') ?></label>
		<select id="<?php echo $this->get_field_id( 'units' ); ?>" name="<?php echo $this->get_field_name( 'units' ); ?>" class="widefat">
		<option value="metric" <?php if ( 'metric' == $instance['units'] ) echo 'selected="selected"'; ?>>Metric</option>
		<option value="imperial" <?php if ( 'imperial' == $instance['units'] ) echo 'selected="selected"'; ?>>Imperial</option>
		</select>
		</p>

        <p>
		<label for="<?php echo $this->get_field_id( 'lang' ); ?>"><?php _e('Language', 'framework') ?></label>
		<select id="<?php echo $this->get_field_id( 'lang' ); ?>" name="<?php echo $this->get_field_name( 'lang' ); ?>" class="widefat">
			<option value="en" <?php selected($instance['lang'], 'en'); ?>>English</option>
			<option value="fr" <?php selected($instance['lang'], 'fr'); ?>>French</option>
			<option value="ru" <?php selected($instance['lang'], 'ru'); ?>>Russian</option>
			<option value="it" <?php selected($instance['lang'], 'it'); ?>>Italian</option>
			<option value="sp" <?php selected($instance['lang'], 'sp'); ?>>Spanish</option>
			<option value="ua" <?php selected($instance['lang'], 'ua'); ?>>Ukrainian</option>
			<option value="de" <?php selected($instance['lang'], 'de'); ?>>German</option>
			<option value="pt" <?php selected($instance['lang'], 'pt'); ?>>Portuguese</option>
			<option value="ro" <?php selected($instance['lang'], 'ro'); ?>>Romanian</option>
			<option value="pl" <?php selected($instance['lang'], 'pl'); ?>>Polish</option>
			<option value="fi" <?php selected($instance['lang'], 'fi'); ?>>Finnish</option>
			<option value="nl" <?php selected($instance['lang'], 'nl'); ?>>Dutch</option>
			<option value="bg" <?php selected($instance['lang'], 'bg'); ?>>Bulgarian</option>
			<option value="se" <?php selected($instance['lang'], 'se'); ?>>Swedish</option>
			<option value="zh_tw" <?php selected($instance['lang'], 'zh_tw'); ?>>Chinese Traditional</option>
			<option value="zh_cn" <?php selected($instance['lang'], 'zh_cn'); ?>>Chinese Simplified</option>
			<option value="tr" <?php selected($instance['lang'], 'tr'); ?>>Turkish</option>
			<option value="cz" <?php selected($instance['lang'], 'cz'); ?>>Czech</option>
			<option value="gl" <?php selected($instance['lang'], 'gl'); ?>>Galician</option>
			<option value="vi" <?php selected($instance['lang'], 'vi'); ?>>Vietnamese</option>
			<option value="ar" <?php selected($instance['lang'], 'ar'); ?>>Arabic</option>
			<option value="mk" <?php selected($instance['lang'], 'mk'); ?>>Macedonian</option>
			<option value="sk" <?php selected($instance['lang'], 'sk'); ?>>Slovak</option>
		</select>
	</p>
	
        <p>
		<label for="<?php echo $this->get_field_id( 'days' ); ?>"><?php _e('Number of days (max is 14)', 'framework') ?></label>
		<input type="text" id="<?php echo $this->get_field_id( 'days' ); ?>" name="<?php echo $this->get_field_name( 'days' ); ?>" value="<?php echo $instance['days']; ?>"  class="widefat" />
	</p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['search'], 'on' ); ?> id="<?php echo $this->get_field_id( 'search' ); ?>" name="<?php echo $this->get_field_name( 'search' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'search' ); ?>"><?php _e('Enable Search', 'theme'); ?></label>
		</p>

   <?php 
}
	} //end class