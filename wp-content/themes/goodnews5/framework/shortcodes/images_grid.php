<?php

if (!class_exists('Mom_images_Shortcodes')) :

class Mom_images_Shortcodes {

	function __construct() {
	
		# Add shortcodes
		add_shortcode('images', array(__CLASS__, 'mom_images'));
		add_shortcode('image', array(__CLASS__, 'image_shortcode'));


	}
	
static function mom_images($atts, $content) {
	extract(shortcode_atts(array(
	'type' => '',
	'auto_slide' => '',
	'auto_duration' => '4000',
	'cols' => 'three',
	'lightbox' => ''
	), $atts));
		ob_start();

		switch ($cols) {
			case 'three':
				$carou_items = 3;
			break;
			case 'four':
				$carou_items = 4;
			break;
			case 'five':
				$carou_items = 5;
			break;
			case 'six':
				$carou_items = 6;
			break;
		}
		$rndn = rand(1,1000);

	wp_enqueue_script('prettyPhoto');
	$script = '';
	?>
	<script>
		jQuery(document).ready(function($){
			$("a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'fast',slideshow:10000, deeplinking: false});
		});
	</script>
<?php
if($type == 'carousel') {
	if ($auto_slide == 'true') {
		$auto_slide = true;
	} else {
		$auto_slide = false;
	}
	if (is_rtl()) {
		$rtl = 'true';
	} else {
		$rtl = 'false';
	}

	$script = '<script>
		jQuery(document).ready(function($){
			 $(".mom_images_grid_'.$rndn.' ul").owlCarousel({
				items:'.$carou_items.',
				baseClass: "mom-carousel",
				autoplay: "'.$auto_slide.'",
				autoplayTimeout: '.$auto_duration.',
				autoplayHoverPause: true,
				rtl: '.$rtl.'
			});
		});
	</script>';

	$igc_start = '<div class="mom_carousel mom_images_grid_'.$rndn.'">';
	$igc_end = '</div>';
	} else {
		$igc_start = '';
		$igc_end = '';
	}
	$pattern = get_shortcode_regex();

    if (   preg_match_all( '/'. $pattern .'/s', $content, $matches )
        && array_key_exists( 2, $matches )
        && in_array( 'image', $matches[2] ) )
    { ?>
	<?php echo $igc_start; ?><div class="mom_images_grid mom_images_<?php echo $cols; ?>_cols"><?php echo $script; ?>
	<ul>
      <?php foreach ($matches[0] as $image) { ?>
                <?php echo do_shortcode($image); ?>
      <?php } ?>
			
		</ul>
	</div><?php echo $igc_end; ?>
<?php } ?>
<?php
	$content = ob_get_contents();
	if (ob_get_contents()) ob_end_clean();
	return $content;

	}
static function image_shortcode($atts, $content = null) {
		extract(shortcode_atts(array(
			'image' => '',
			'link' => '',
		), $atts));
				ob_start();

				$rel = 'rel="prettyPhoto[img_grid]"';
			$thumb = $image;
			if ($link == $thumb ) {
				$link = wp_get_attachment_image_src($thumb, 'full');
				$link = $link[0];
			}
			$thumb = wp_get_attachment_image_src($thumb, 'big-wide-img');
			$thumb = $thumb[0];
?>
                        <li><a href="<?php echo $link; ?>" <?php echo $rel; ?>><img src="<?php echo $thumb; ?>" alt=""></a></li>
<?php
		$output = ob_get_contents();
		if (ob_get_contents()) ob_end_clean();
		return $output;
}

}

$Mom_images_Shortcodes = new Mom_images_Shortcodes;

endif;