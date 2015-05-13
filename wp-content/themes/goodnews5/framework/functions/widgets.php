<?php
/*
  Forked from : https://wordpress.org/extend/plugins/widget-title-links/
  Many Thanks to plugin author : ragulka

*/
class mom_Widget_custom_fields {

  public function __construct() {
    load_plugin_textdomain( 'widget-title-links', false, basename( dirname( __FILE__ ) ) . '/languages' );

    add_action( 'in_widget_form', array( $this, 'add_custom_fields_to_widget_form' ), 1, 3 );
    
    add_filter( 'widget_form_callback', array( $this, 'register_widget_custom_field'), 10, 2 );
    add_filter( 'widget_update_callback', array( $this, 'widget_update_extend'), 10, 2 );
    add_filter( 'dynamic_sidebar_params', array( $this, 'widget_custom_field_output'), 99, 2 );
    
  }

  /**
   * Add Momizat custom fields to widget form
   *
   * @since 1.0
   * @uses add_action() 'in_widget_form'
   */
  public function add_custom_fields_to_widget_form( $widget, $args, $instance ) {
  ?>
  <div class="momizat_widget_custom_fields">
    <h4 class="custom_colors_title"><?php _e('Header Custom Color','theme'); ?></h4>
    <fieldset>
      <p><label for="<?php echo $widget->get_field_id('header_background'); ?>"><?php _e('Header Background color', 'theme'); ?></label>
      <input type="text" name="<?php echo $widget->get_field_name('header_background'); ?>" id="<?php echo $widget->get_field_id('header_background'); ?>"" class="widefat mom-color-field" value="<?php echo $instance['header_background']; ?>"" /></p>
      <p><label for="<?php echo $widget->get_field_id('header_text_color'); ?>"><?php _e('Header Text color', 'theme'); ?></label>
      <input type="text" name="<?php echo $widget->get_field_name('header_text_color'); ?>" id="<?php echo $widget->get_field_id('header_text_color'); ?>"" class="widefat mom-color-field" value="<?php echo $instance['header_text_color']; ?>"" /></p>
        <p>
        <input type="checkbox" class="checkbox" id="<?php echo $widget->get_field_id('hide_dots'); ?>" name="<?php echo $widget->get_field_name('hide_dots'); ?>"<?php checked( $instance['hide_dots'] ); ?> />
        <label for="<?php echo $widget->get_field_id('hide_dots'); ?>"><?php _e( 'Hide dots pattern', 'theme' ); ?></label>
      </p>

      
    </fieldset>
  </div>
  <?php
  }

  /**
   * Register the additional widget field
   *
   * @since 1.0
   * @uses add_filter() 'widget_form_callback'
   */
  public function register_widget_custom_field ( $instance, $widget ) {
    if ( !isset($instance['header_background']) )
      $instance['header_background'] = null;
    if ( !isset($instance['header_text_color']) )
      $instance['header_text_color'] = null;
    if ( !isset($instance['hide_dots']) )
      $instance['hide_dots'] = null;
    return $instance;
  }

  /**
   * Add the additional field to widget update callback
   *
   * @since 1.0
   * @uses add_filter() 'widget_update_callback'
   */
  public function widget_update_extend ( $instance, $new_instance ) {
    $instance['header_background'] =  $new_instance['header_background'];
    $instance['header_text_color'] =  $new_instance['header_text_color'];
    $instance['hide_dots'] = !empty($new_instance['hide_dots']) ? 1 : 0;
    return $instance;
  }

  /**
   * Momizat custom fields output
   *
   * Title link should be set by editors 
   * in widget settings in Appearance->Widgets
   *
   * @since 1.o
   * @uses add_filter() 'dynamic_sidebar_params'
   */
  public function widget_custom_field_output( $params ) {
    if (is_admin())
      return $params;

    global $wp_registered_widgets;
    $id = $params[0]['widget_id'];

    if (isset($wp_registered_widgets[$id]['callback'][0]) && is_object($wp_registered_widgets[$id]['callback'][0])) {
      // Get settings for all widgets of this type
      $settings = $wp_registered_widgets[$id]['callback'][0]->get_settings();

      // Get settings for this instance of the widget
      $instance = $settings[substr( $id, strrpos( $id, '-' ) + 1 )];

      // Allow overriding the title link programmatically via filters
      $hbg = isset($instance['header_background']) ? $instance['header_background'] : null;
      $htx = isset($instance['header_text_color']) ? $instance['header_text_color'] : null;
      if (isset($instance['hide_dots'])) {
        $dots = $instance['hide_dots'] ? true : false;
      } else {
        $dots = '';
      }
      if ($hbg != '') {
        $hbg = 'style="background:'.$hbg.';"';
      }
      if ($dots != false) {
        $dots = 'background:none;';
      }
      if ($htx != '') {
        $htx_out = 'style="color:'.$htx.';'.$dots.'"';
      } else {
        $htx_out = 'style="'.$dots.';"';
      }
      if(isset($params[0]['id']) && strpos($params[0]['id'],'footer') === false){
        if ($hbg != '' || $htx != '' || $dots != false) {
          $params[0]['before_title'] = '<div class="widget-head" '.$hbg.'><h3 class="widget-title" '.$htx_out.'><span '.$hbg.'>';
        }
      }
    }

    return $params;
  }
}

new mom_Widget_custom_fields();