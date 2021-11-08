<?php
/*
** CISE Faculty Research Videos admin panel customizations
**
*/


/*Add in custom columns in the admin panel*/
add_filter( 'manage_edit-faculty-research-videos_columns', 'facresvids_columns' ) ;

function facresvids_columns( $columns ) {

	$columns = array(
		'cb' => '&lt;input type="checkbox" />',
		'name' => __( 'Name' ),
		'photo' => __( 'Photo' ),
		'date' => __( 'Date' )		
	);

	return $columns;
}

add_action( 'manage_faculty-research-videos_posts_custom_column', 'manage_facresvids_columns', 10, 2 );

/*Pull in data for the custom columns*/
function manage_facresvids_columns( $column, $post_id ) {
	global $post;

	switch( $column ) {

		/* If displaying the 'name' column. */
		case 'name' :

			/* Get the post meta. */
			$name = get_post_meta( $post_id, 'psjm_name', true );

			/* Display the post meta. */
			printf( $name );

			break;
			
		/* If displaying the 'photo' column. */
		case 'photo' :

			/* Get the post meta. */
			$image = get_post_thumbnail();
			$photo = get_post_meta( $post_id, $image, true );

			/* Display the post meta. */
			printf( '<a href="' . $photo . '">Photo</a>');

			break;			

		/* Just break out of the switch statement for everything else. */
		default :
			break;
	}
}

//Make columns sortable in the Admin Edit panel
add_filter( 'manage_edit-faculty-research-videos_sortable_columns', 'facresvids_sortable_columns' ) ;

function facresvids_sortable_columns( $columns ) {

	$columns['name'] = 'Name';

	return $columns;
}

// Only run our customization on the 'edit.php' page in the admin.
add_action( 'load-edit.php', 'my_edit_facresvids_load' );

function my_edit_facresvids_load() {
	add_filter( 'request', 'my_sort_facresvids' );
}

// Sorts the custom faculty research videos columns.
function my_sort_facresvids( $vars ) {

	/* Check if we're viewing the 'faculty-research-videos' post type. */
	if ( isset( $vars['post_type'] ) && 'faculty-research-videos' == $vars['post_type'] ) {

		/* Check if 'orderby' is set to 'name'. */
		if ( isset( $vars['orderby'] ) && 'Name' == $vars['orderby'] ) {

			/* Merge the query vars with our custom variables. */
			$vars = array_merge(
				$vars,
				array(
					'meta_key' => 'title',
					'orderby' => 'meta_value'
				)
			);
		}
	}

	return $vars;
}

//Customize the search of admin panel edit page
add_filter( 'posts_join', 'facresvids_search' );
function facresvids_search ( $join ) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "faculty-research-videos".
    if ( is_admin() && 'edit.php' === $pagenow && 'faculty-research-videos' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {    
        $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }
    return $join;
}

add_filter( 'posts_where', 'facresvids_search_where' );
function facresvids_search_where( $where ) {
    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "faculty-research-videos".
    if ( is_admin() && 'edit.php' === $pagenow && 'faculty-research-videos' === $_GET['post_type'] && ! empty( $_GET['s'] ) ) {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)", $where );
    }
    return $where;
}

function facresvids_search_distinct( $where ){
    global $pagenow, $wpdb;

    if ( is_admin() && $pagenow=='edit.php' && $_GET['post_type']=='faculty-research-videos' && $_GET['s'] != '') {
    return "DISTINCT";

    }
    return $where;
}
add_filter( 'posts_distinct', 'facresvids_search_distinct' );
//Ends search of admin panel edit page