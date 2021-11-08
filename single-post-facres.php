<?php
/**
 * Template Name: Faculty Research Page
 * Template Post Type: post
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package HWCOE_UFL_CHILD
 */
get_header(); ?>

<div id="main" class="container main-content">
<div class="row">
	<div class="col-md-12">
	  <?php 
			if(get_field('video_link')){ //if the field is not empty
				echo '<p>' . get_field('video_link') . '</p>'; //display it
			} 
		?>
	</div>
	<div class="col-md-12">
	<?php if ( get_field( 'lab_weblink') ): ?>
		<p><a href="<?php esc_url( the_field( 'lab_weblink' ) ); ?>" target="_blank"><?php esc_attr( the_field( 'lab_weblink' ) ); ?></a></p>
	<?php endif ?>
	</div>
</div>
</div>

<?php get_footer(); ?>
