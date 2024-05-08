<?php
/*
Plugin Name: HWCOE Faculty Research Videos
Description: Use this shortcode to display faculty research videos under the "fac-research" category<strong>[FACULTY-RESEARCH type="facresvideos" posts_per_page="60" order="ASC" orderby="title"]</strong>
Version: 1.1
Author: Allison Logan
Author URI: http://allisoncandreva.com/
*/

function facresvid_create_post_type() {
  register_post_type( 'facresvideos',
    array(
		'labels' => array(
			'name' => __( 'Faculty Research Videos' ), //Top of page when in post type
			'singular_name' => __( 'Faculty Research Videos' ), //per post
			'menu_name' => __('Faculty Research Videos'), //Shows up on side menu
			'all_items' => __('All Entries'), //On side menu as name of all items
		  ),
		'supports' => array( 'title', 'thumbnail' ),
		'public' => true,
		'menu_position' => 9,
		'menu_icon' => 'dashicons-id-alt',
		'has_archive' => true,
    )
  );
}
add_action( 'init', 'facresvid_create_post_type' );

if( is_admin() ){
    include( 'admin-entries.php' );
}

Class FacultyResearch {

	public $plugin_dir;
	public $plugin_url;
	
	function  __construct(){
		global $wpdb;
		$prefix = $wpdb->prefix;
		$this->plugin_dir = plugin_dir_path(__FILE__);
		$this->plugin_url = plugin_dir_url(__FILE__);
		add_shortcode( 'FACULTY-RESEARCH', array($this, 'faculty_research_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array($this,'facres_enqueue_scripts_styles' ));
	}
	
	function facres_enqueue_scripts_styles(){
		wp_enqueue_script('facres_fancybox_js', $this->plugin_url.'js/jquery.fancybox.min.js', array('jquery'), '1.0.0', true);
		wp_enqueue_style('facres_fancybox_css', $this->plugin_url.'css/jquery.fancybox.min.css');
		wp_enqueue_script('facres', $this->plugin_url.'js/facres.js', array('jquery'), '1.0.0', true);
		wp_enqueue_style('facres', $this->plugin_url.'css/facres.css');
	}
		
	public function faculty_research_shortcode($atts) {

		extract( shortcode_atts( array(
			'posts_per_page' => '60',
			'order' => 'ASC',
			'orderby' => 'title',
			'type'=>'facresvideos',	
		), $atts ) );
		
		$args = array(
			'posts_per_page' => (int) $atts['posts_per_page'],
			'post_type' =>$atts['type'],
			'order' => $atts['order'],
			'orderby' => $atts['orderby'],
			'no_found_rows' => true,
		);
		
		$dispCount  = (int) $posts_per_page;
		if($dispCount==60){
			$colmd = 'three';
		}else if($dispCount=="3"){
			$colmd = 'three'; 
		}else{
			$colmd = 'three';
		}
		$query = new WP_Query( $args  );

		$output = '<div class="faculty-research-tab">'; //col-md-12

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) : $query->the_post();
				$post_id = get_the_ID();
			
				$output .= '<div class="facresbox">';				
				$output .= $this->wpse69204_excerpt(); 
				$output .= 
					'<div class="fancyboxcont" id="post_'.$post_id.'">';
				$output .= '<div class="col-md-12 popupcont">
					<div class="facrescont">' .get_field('video_link');
			if(get_field( 'lab_weblink' )):  //if the field is not empty
				$output .= '<p>Lab: <a href="' .get_field('lab_weblink'). '" target="_blank">' .get_field('lab_weblink'). '</a></p>'; //display it
				else: 
				$output .= '';
				endif;
				$output .= '</div></div></div></div>';
			endwhile;
			wp_reset_postdata();
		} else { ?>
			<p style="text-align:center;">There are no research videos at this time. Please come back.</p>
		<?php }
		$output .= '</div>';
		return $output;
	} //end faculty_research_shortcode function
	
	public function wpse69204_excerpt( $post_id = null )
	{
		global $post;
		$current_post = $post_id ? get_post( $post_id ) : $post;
		$featimageURL = wp_get_attachment_url( get_post_thumbnail_id($current_post) );
		$feat_image       = ( !empty($featimageURL) ) ?  '<img src="'.$featimageURL.'" title="'.get_field('display_name').'"> ':'';
		$excerpt .= '<a class="various" data-fancybox="facultyresvideos" href="#post_'.$post->ID.'" title="'.get_field('display_name').'"><div class="fitimage">' .$feat_image. '</div><h1>' .get_field('display_name'). '</h1></a>';
		return $excerpt;
	}
}

$FacultyResearch = new FacultyResearch();

// Add field groups for HWCOE Faculty Research Videos

add_filter('acf/settings/save_json', 'hwcoe_facresvideos_acf_json_save_point');

if (!function_exists('hwcoe_facresvideos_acf_json_save_point')) { 
	function hwcoe_facresvideos_acf_json_save_point( $path ) {
		// update path
		$paths[] = plugin_dir_path(__FILE__) . 'inc/acf-json';
		return $path; 
	}
}

add_filter('acf/settings/load_json', 'hwcoe_facresvideos_acf_json_load_point');

if (!function_exists('hwcoe_facresvideos_acf_json_load_point')) {
	function hwcoe_facresvideos_acf_json_load_point( $paths ) {	
		// remove original path (optional)
		unset($paths[0]);

		// append path
		$paths[] = plugin_dir_path(__FILE__) . 'inc/acf-json';
		
		// return
		return $paths;
	}
}
