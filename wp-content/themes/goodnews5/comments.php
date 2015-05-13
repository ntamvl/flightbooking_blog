<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to twentytwelve_comment() which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h2 class="single-title">
			<?php
				printf( _n( 'One Comment', '%1$s Comments', get_comments_number(), 'theme' ),
					number_format_i18n( get_comments_number() ));
			?>
		</h2>

		<ol class="comments_list">
			<?php wp_list_comments( array( 'callback' => 'mom_comment', 'style' => 'ol' ) ); ?>
		</ol><!-- .commentlist -->

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="navigation" role="navigation">
			<h1 class="assistive-text section-heading"><?php _e( 'Comment navigation', 'theme' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'theme' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'theme' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<?php
		/* If there are no comments and comments are closed, let's leave a note.
		 * But we only want the note on posts and pages that had comments in the first place.
		 */
		if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="nocomments"><?php _e( 'Comments are closed.' , 'theme' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>
	<?php
	$commenter = wp_get_current_commenter();
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$required_text = sprintf( ' ' . __('Những field bắt buộc đuợc đánh dấu %s'), '<span class="required">*</span>' );
	$args = array(
	'id_form' => 'commentform',
	'id_submit' => 'submit-comment',
	'title_reply' => __( 'Gởi ý kiến của bạn' ),
	'title_reply_to' => __( 'Gởi ý kiến cho %s' ),
	'cancel_reply_link' => __( 'Cancel Reply' ),
	'label_submit' => __( 'Gửi ý kiến' ),
	'comment_field' => '<p class="comment-form-comment"><textarea id="comment" placeholder="'.__('Nội dung ý kiến của bạn...', 'theme').'" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>',
	'must_log_in' => '<p class="must-log-in">' .  sprintf( __( 'You must be <a href="%s">logged in</a> to post a comment.' ), wp_login_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
	'logged_in_as' => '<p class="logged-in-as">' . sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>' ), admin_url( 'profile.php' ), $user_identity, wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) ) ) . '</p>',
	'comment_notes_before' => '<p class="comment-notes">' . __( 'Email của bạn sẽ không đuợc công khai.' ) . ( $req ? $required_text : '' ) . '</p>',
	'comment_notes_after' => '',
	'fields' => apply_filters( 'comment_form_default_fields', array(
		'author' => '<input id="author" name="author" type="text" placeholder="'.__('Họ tên (*)', 'theme').'" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' />',
		'email' => '<input id="email" name="email" type="text" placeholder="'.__('Email (*)', 'theme').'" value="' . esc_attr(  $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' />',
		'url' => '<input id="url" name="url" type="text" placeholder="'.__('Website', 'theme').'" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" />' ) ) );
	?>

	<?php comment_form($args); ?>

</div><!-- #comments .comments-area -->