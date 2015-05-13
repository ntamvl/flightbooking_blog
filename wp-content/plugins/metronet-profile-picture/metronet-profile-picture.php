<?php
/*
Plugin Name: Metronet Profile Picture
Plugin URI: http://wordpress.org/extend/plugins/metronet-profile-picture/
Description: Use the native WP uploader on your user profile page.
Author: Metronet
Version: 1.2.0
Requires at least: 3.5
Author URI: http://www.metronet.no
Contributors: ronalfy, metronet
Text Domain: metronet-profile-picture
Domain Path: /languages
*/ 

class Metronet_Profile_Picture	{	
	
	//private
	private $plugin_url = '';
	private $plugin_dir = '';
	private $plugin_path = '';
	
	/**
	* __construct()
	* 
	* Class constructor
	*
	*/
	function __construct(){
			
		//* Localization Code */
		load_plugin_textdomain( 'metronet-profile-picture', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		$this->plugin_path = plugin_basename( __FILE__ );
		$this->plugin_url = rtrim( plugin_dir_url(__FILE__), '/' );
		$this->plugin_dir = rtrim( plugin_dir_path(__FILE__), '/' );
		
		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'personal_options', array( &$this, 'insert_upload_form' ) );
		
		//Scripts
		add_action( 'admin_print_scripts-user-edit.php', array( &$this, 'print_media_scripts' ) );
		add_action( 'admin_print_scripts-profile.php', array( &$this, 'print_media_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'profile_print_media_scripts' ) );
		
		//Styles
		add_action( 'admin_print_styles-user-edit.php', array( &$this, 'print_media_styles' ) );
		add_action( 'admin_print_styles-profile.php', array( &$this, 'print_media_styles' ) );
		
		//Ajax
		add_action( 'wp_ajax_metronet_add_thumbnail', array( &$this, 'ajax_add_thumbnail' ) );
		add_action( 'wp_ajax_metronet_get_thumbnail', array( &$this, 'ajax_get_thumbnail' ) );
		add_action( 'wp_ajax_metronet_remove_thumbnail', array( &$this, 'ajax_remove_thumbnail' ) );
		
		//User update action
		add_action( 'edit_user_profile_update', array( &$this, 'save_user_profile' ) );
		add_action( 'personal_options_update', array( &$this, 'save_user_profile' ) );
		
		
		//User Avatar override
		add_filter( 'get_avatar', array( &$this, 'avatar_override' ), 10, 5 );
	} //end constructor
	
	/**
	* ajax_add_thumbnail()
	*
	* Adds a thumbnail to user meta and returns thumbnail html
	*
	*/
	public function ajax_add_thumbnail() {
		$post_id = isset( $_POST[ 'post_id' ] ) ? absint( $_POST[ 'post_id' ] ) : 0;
		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : 0;
		$thumbnail_id = isset( $_POST[ 'thumbnail_id' ] ) ? absint( $_POST[ 'thumbnail_id' ] ) : 0;
		if ( $post_id == 0 || $user_id == 0 || $thumbnail_id == 0 ) die( '' );
		check_ajax_referer( "update-post_$post_id" );
		
		//Save user meta
		update_user_option( $user_id, 'metronet_post_id', $post_id );
		update_user_option( $user_id, 'metronet_image_id', $thumbnail_id ); //Added via this thread (Props Solinx) - https://wordpress.org/support/topic/storing-image-id-directly-as-user-meta-data
		set_post_thumbnail( $post_id, $thumbnail_id );

		if ( has_post_thumbnail( $post_id ) ) {
			$post_thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail' );
			$crop_html = $this->get_post_thumbnail_editor_link( $post_id );
			$thumb_html = sprintf( '<a href="#" class="mpp_add_media">%s</a>', $post_thumbnail );
			$thumb_html .= sprintf( '<a id="metronet-remove" class="dashicons dashicons-trash" href="#" title="%s">%s</a>', esc_attr__( 'Remove profile image', 'metronet_profile_picture' ), esc_html__( "Remove profile image", "metronet_profile_picture" ) );
			die( json_encode( array(
				'thumb_html' => $thumb_html,
				'crop_html' => $crop_html,
				'has_thumb' => true
			) ) );
		}
		die( json_encode( array( 'thumb_html' => '', 'crop_html' => '', 'has_thumb' => false ) ) );
	} //end ajax_add_thumbnail
	
	/**
	* ajax_get_thumbnail()
	*
	* Retrieves a thumbnail based on a passed post id ($_POST)
	*
	*/
	public function ajax_get_thumbnail() {
		$post_id = isset( $_POST[ 'post_id' ] ) ? absint( $_POST[ 'post_id' ] ) : 0;

		if ( has_post_thumbnail( $post_id ) ) {
			$post_thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail' );
			$crop_html = $this->get_post_thumbnail_editor_link( $post_id );
			$thumb_html = sprintf( '<a href="#" class="mpp_add_media">%s</a>', $post_thumbnail );
			$thumb_html .= sprintf( '<a id="metronet-remove" class="dashicons dashicons-trash" href="#" title="%s">%s</a>', esc_attr__( 'Remove profile image', 'metronet_profile_picture' ), esc_html__( "Remove profile image", "metronet_profile_picture" ) );
			die( json_encode( array(
				'thumb_html' => $thumb_html,
				'crop_html' => $crop_html,
				'has_thumb' => true
			) ) );
		} else {
			$thumb_html = '<a href="#" class="mpp_add_media">';
			$thumb_html.= sprintf( '<img src="%s" width="150" height="150" title="%s" />', $this->get_plugin_url( 'img/mystery.png' ), esc_attr__( "Upload or Change Profile Picture", 'metronet_profile_picture' ) );
			$thumb_html .= '</a>';	
		}
		die( json_encode( array( 'thumb_html' => $thumb_html, 'crop_html' => '', 'has_thumb' => false ) ) );
	} //end ajax_get_thumbnail
	
	/**
	* ajax_remove_thumbnail()
	*
	* Removes a featured thumbnail
	*
	*/
	public function ajax_remove_thumbnail() {
		$post_id = isset( $_POST[ 'post_id' ] ) ? absint( $_POST[ 'post_id' ] ) : 0;
		$user_id = isset( $_POST[ 'user_id' ] ) ? absint( $_POST[ 'user_id' ] ) : 0;
		if ( $post_id == 0 || $user_id == 0 ) die( '' );
		check_ajax_referer( "update-post_$post_id" );
		
		$thumb_html = '<a href="#" class="mpp_add_media">';
		$thumb_html.= sprintf( '<img src="%s" width="150" height="150" title="%s" />', $this->get_plugin_url( 'img/mystery.png' ), esc_attr__( "Upload or Change Profile Picture", 'metronet_profile_picture' ) );
		$thumb_html .= '</a>';

		//Save user meta and update thumbnail
		update_user_option( $user_id, 'metronet_image_id', 0 ); 
		delete_post_meta( $post_id, '_thumbnail_id' );
		die( json_encode( array( 'thumb_html' => $thumb_html, 'crop_html' => '' ) ) );
	}
	
	/**
	* avatar_override()
	*
	* Overrides an avatar with a profile image
	*
	* @param string $avatar SRC to the avatar
	* @param mixed $id_or_email 
	* @param int $size Size of the image
	* @param string $default URL to the default image
	* @param string $alt Alternative text
	**/
	public function avatar_override( $avatar, $id_or_email, $size, $default, $alt ) {
		global $pagenow;
		if ( 'options-discussion.php' == $pagenow ) return $avatar; //Stop overriding gravatars on options-discussion page
		
		//Get user data
		if ( is_numeric( $id_or_email ) ) {
			$user = get_user_by( 'id', ( int )$id_or_email );
		} elseif( is_object( $id_or_email ) )  {
			$comment = $id_or_email;
			if ( empty( $comment->user_id ) ) {
				$user = get_user_by( 'id', $comment->user_id );
			} else {
				$user = get_user_by( 'email', $comment->comment_author_email );
			}
			if ( !$user ) return $avatar;
		} elseif( is_string( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
		} else {
			return $avatar;
		}
		if ( !$user ) return $avatar;
		$user_id = $user->ID;
				
		//Determine if user has an avatar override
		$avatar_override = get_user_option( 'metronet_avatar_override', $user_id );
		if ( !$avatar_override || $avatar_override != 'on' ) return $avatar;
		
		//Determine if the user has a profile image
		
		$custom_avatar = mt_profile_img( $user_id, array( 
			'size' => array( $size, $size ), 
			'attr' => array( 'alt' => $alt, 'class' => "avatar avatar-{$size} photo" ), 
			'echo' => false )
		);
		
		if ( !$custom_avatar ) return $avatar; 
		return $custom_avatar;	
	} //end avatar_override
	
	/**
	* get_plugin_dir()
	* 
	* Returns an absolute path to a plugin item
	*
	* @param		string    $path	Relative path to make absolute (e.g., /css/image.png)
	* @return		string               An absolute path (e.g., /htdocs/ithemes/wp-content/.../css/image.png)
	*/
	public function get_plugin_dir( $path = '' ) {
		$dir = $this->plugin_dir;
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;		
	} //end get_plugin_dir
	
	
	/**
	* get_plugin_url()
	* 
	* Returns an absolute url to a plugin item
	*
	* @param		string    $path	Relative path to plugin (e.g., /css/image.png)
	* @return		string               An absolute url (e.g., http://www.domain.com/plugin_url/.../css/image.png)
	*/
	public function get_plugin_url( $path = '' ) {
		$dir = $this->plugin_url;
		if ( !empty( $path ) && is_string( $path) )
			$dir .= '/' . ltrim( $path, '/' );
		return $dir;	
	} //get_plugin_url
	
	/**
	* get_post_id
	*
	* Gets a post id for the user - Creates a post if a post doesn't exist
	* 
	@param int user_id User ID of the user
	@return int post_id
	*/
	private function get_post_id( $user_id = 0 ) {
		//Get/Create Profile Picture Post
		$post_args = array(
			'post_type' => 'mt_pp',
			'author' => $user_id, 
			'post_status' => 'publish'
		);
		$posts = get_posts( $post_args );
		if ( !$posts ) {
			$post_id = wp_insert_post( array(
				'post_author' => $user_id,
				'post_type' => 'mt_pp',
				'post_status' => 'publish',
			) );
		} else {
			$post = end( $posts );
			$post_id = $post->ID;
		}
		return $post_id;
	} //end get_post_id
	
	/**
	* get_post_thumbnail_editor_link
	*
	* Retrieve a crop-image link (HTML) based on the passed post_id
	* 
	@param int post_id Post ID to find the featured image for
	@return string html
	*/
	private function get_post_thumbnail_editor_link( $post_id ) {
		ob_start();
		if ( has_post_thumbnail( $post_id ) && defined( 'PTE_VERSION' ) ) {
			//Post Thumbnail Editor compatibility - http://wordpress.org/extend/plugins/post-thumbnail-editor/
			$post_thumbnail_id = get_post_meta( $post_id, '_thumbnail_id', true );
			$pte_url = add_query_arg( array(
				'page' => 'pte-edit',
				'pte-id' => $post_thumbnail_id
			), admin_url('upload.php') );
			printf( ' - <a href="%s">%s</a>', $pte_url, __( 'Crop Thumbnail', 'metronet_profile_picture' ) );							
		} //end post thumbnail editor
		return ob_get_clean();
	} //end get_post_thumbnail_editor_link
	
	/**
	* get_user_id
	*
	* Gets a user ID for the user
	* 
	*@return int user_id
	* 
	@return int post_id
	*/
	private function get_user_id() {
		//Get user ID
		$user_id = isset( $_GET[ 'user_id' ] ) ? absint( $_GET[ 'user_id' ] ) : 0;
		if ( $user_id == 0 && IS_PROFILE_PAGE ) {
			$current_user = wp_get_current_user();
			$user_id = $current_user->ID;
		}
		return $user_id;
	} //end get_user_id
		
	/**
	* init()
	* 
	* Initializes plugin localization, post types, updaters, plugin info, and adds actions/filters
	*
	*/
	function init() {		
		
		add_theme_support( 'post-thumbnails' ); //This should be part of the theme, but the plugin registers it just in case.
		//Register post types
		$post_type_args = array(
			'public' => false,
			'publicly_queryable' => true,
			'show_ui' => false,
			'show_in_menu' => false,
			'query_var' => true,
			'rewrite' => false,
			'has_archive' => false,
			'hierarchical' => false,
			'supports' => array( 'thumbnail' )
		);
		register_post_type( 'mt_pp', $post_type_args );
		
	}//end function init
	
	
	
	/**
	* insert_upload_form
	*
	* Adds an upload form to the user profile page and outputs profile image if there is one
	*/
	public function insert_upload_form() {
		if ( !current_user_can( 'upload_files' ) ) return; //Users must be author or greater
		
		$user_id = $this->get_user_id();
		$post_id = $this->get_post_id( $user_id );
		
		?>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( "Profile Image", "metronet_profile_picture" ); ?></th>
			<td id="mpp">
				<input type="hidden" name="metronet_profile_id" id="metronet_profile_id" value="<?php echo esc_attr( $user_id ); ?>" />
				<input type="hidden" name="metronet_post_id" id="metronet_post_id" value="<?php echo esc_attr( $post_id ); ?>" />
				<div id="metronet-profile-image">
				<?php
					$has_profile_image = false;
					if ( has_post_thumbnail( $post_id ) ) {
						$has_profile_image = true;
						echo '<a href="#" class="mpp_add_media">';
						$post_thumbnail = get_the_post_thumbnail( $post_id, 'thumbnail' );
						echo $post_thumbnail;
						echo '</a>';
					} else {
						echo '<a href="#" class="mpp_add_media">';
						$post_thumbnail = sprintf( '<img src="%s" width="150" height="150" title="%s" />', $this->get_plugin_url( 'img/mystery.png' ), esc_attr__( "Upload or Change Profile Picture", 'metronet_profile_picture' ) );
						echo $post_thumbnail;
						echo '</a>';
					}
					$remove_classes = array( 'dashicons', 'dashicons-trash' );
					if ( !$has_profile_image ) {
						$remove_classes[] = 'mpp-no-profile-image';	
					}
				?>
					<a id="metronet-remove" class="<?php echo implode( ' ', $remove_classes ); ?>" href="#" title="<?php esc_attr_e( 'Remove profile image', 'metronet_profile_picture' ); ?>"><?php esc_html_e( "Remove profile image", "metronet_profile_picture" );?></a>
				</div><!-- #metronet-profile-image -->
				<div id="metronet-override-avatar">
					<input type="hidden" name="metronet-user-avatar" value="off" /> 
					<?php
					//Get the user avatar override option - If not set, see if there's a filter override.
					$user_avatar_override = get_user_option( 'metronet_avatar_override', $user_id );
					$checked = '';
					if ( $user_avatar_override ) {
						$checked = checked( 'on', $user_avatar_override, false );
					} else {
						$checked = checked( true, apply_filters( 'mpp_avatar_override', false ), false );
					}
					
					//Filter for hiding the override interface.  If this option is set to true, the mpp_avatar_override filter is ignored and override is enabled by default
					$hide_override = apply_filters( 'mpp_hide_avatar_override', false );
					if ( $hide_override ):
					?>
					<input type="hidden" name="metronet-user-avatar" id="metronet-user-avatar" value="on"  />
					<?php
					else:
					?>
					<br /><input type="checkbox" name="metronet-user-avatar" id="metronet-user-avatar" value="on" <?php echo $checked; ?> /><label for="metronet-user-avatar"> <?php esc_html_e( "Override Avatar?", "metronet_profile_picture" ); ?></label>
					<?php endif; ?>
				</div><!-- #metronet-override-avatar -->
			</td>
		</tr>
		<?php
	} //end insert_upload_form
	
	function profile_print_media_scripts() {
		if ( defined( 'IS_PROFILE_PAGE' ) && IS_PROFILE_PAGE == true ) {
			$this->print_media_scripts();
		}	
	}
	/**
	* print_media_scripts
	*
	* Output media scripts for thickbox and media uploader
	**/
	public function print_media_scripts() {
		$post_id = $this->get_post_id( $this->get_user_id() );
		wp_enqueue_media( array( 'post' => $post_id ) );
		$script_deps = array( 'media-editor' );
		wp_enqueue_script( 'mt-pp', $this->get_plugin_url( '/js/mpp.js' ), $script_deps, '20141111', true );
		wp_localize_script( 'mt-pp', 'metronet_profile_image', 
			array( 
				'set_profile_text' => __( 'Set profile image', 'metronet_profile_picture' ),
				'remove_profile_text' => __( 'Remove profile image', 'metronet_profile_picture' ),
				'crop' => __( 'Crop Thumbnail', 'metronet_profile_picture' ),
				'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) )
			) 
		);
		?>
		<style>
		/* Metronet Profile Picture */
		#metronet-profile-image {
			position: relative;
			float: left;	
		}
		#metronet-remove {
			position: absolute;
			background: #424242;
			top: 0;
			right: 0;
			display: block;
			padding: 3px;
			width: 20px;
			height: 20px;
			overflow: hidden;
		}
		#metronet-remove:before {
			content: "\f182";
			color: #fd6a6a;
			font-size: 20px;
		}
		#metronet-remove:hover:before {
			color: #ff3e3e;
		}
		#metronet-remove.mpp-no-profile-image {
			display: none;	
		}
		#metronet-override-avatar {
			clear: left;	
		}
		</style>
		<?php
	} //end print_media_scripts
	
	public function print_media_styles() {
	} //end print_media_styles
	
	/**
	* save_user_profile()
	*
	* Saves user profile fields
	* @param int $user_id 
	**/
	public function save_user_profile( $user_id ) {
		if ( !isset( $_POST[ 'metronet-user-avatar' ] ) ) return;
		check_admin_referer( 'update-user_' . $user_id );
		
		$user_avatar = $_POST[ 'metronet-user-avatar' ];
		if ( $user_avatar == 'on' ) {
			update_user_option( $user_id, 'metronet_avatar_override', 'on' );
		} else {
			update_user_option( $user_id, 'metronet_avatar_override', 'off' );
		}
	} //end save_user_profile
	
} //end class
//instantiate the class
global $mt_pp;
if (class_exists('Metronet_Profile_Picture')) {
	if (get_bloginfo('version') >= "3.5") {
		add_action( 'plugins_loaded', 'mt_mpp_instantiate' );
	}
}
function mt_mpp_instantiate() {
	global $mt_pp;
	$mt_pp = new Metronet_Profile_Picture();
}
/**
* mt_profile_img
* 
* Adds a profile image
*
@param $user_id INT - The user ID for the user to retrieve the image for
@ param $args mixed
	size - string || array (see get_the_post_thumbnail)
	attr - string || array (see get_the_post_thumbnail)
	echo - bool (true or false) - whether to echo the image or return it
*/
function mt_profile_img( $user_id, $args = array() ) {
	$profile_post_id = absint( get_user_option( 'metronet_post_id', $user_id ) );
	
	$defaults = array(
		'size' => 'thumbnail',
		'attr' => '',
		'echo' => true
	);
	$args = wp_parse_args( $args, $defaults );
	extract( $args );
	//Return false or echo nothing if there is no post thumbnail
	if( !has_post_thumbnail( $profile_post_id ) ) {
		if ( $echo ) echo '';
		else return false;
		return;
	}
	$post_thumbnail = get_the_post_thumbnail( $profile_post_id, $size, $attr );
	if ( $echo ) {
		echo $post_thumbnail;
	} else {
		return $post_thumbnail;
	}
} //end mt_profile_img
?>