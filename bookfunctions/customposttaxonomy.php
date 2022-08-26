<?php
// CREATE BOOK CUSTOM POST TYPE

 //class SetUpFunctions{
	function create_book_type() {
		register_post_type( 'book',
		// CPT Options
			array(
				'labels' => array(
					'name' => __( 'Books/Chapters','default' ),
					'singular_name' => __( 'Book','default' )
				),
				'taxonomies'          => array( 'subject', 'post_tag' ),
				'hierarchical'        => true,
				'public' => true,
				'has_archive' => true,
				'rewrite' => [
					'slug' => '/',
					'with_front' => false
				],
				'menu_icon'  => 'dashicons-book',
				'show_in_rest' => true,
				'menu_position'       => 4,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'capability_type'     => 'page',
			)
		);
	}
	// Hooking up our function to theme setup
	add_action( 'init', 'create_book_type' );

	/* ALLOW CATEGORIES AND TAGS FOR BOOKS */
	function add_custom_taxonomies() {
		// Add new "Subject" taxonomy to Posts
		register_taxonomy('subject', 'book', array(
		  // Hierarchical taxonomy (like categories)
		  'hierarchical' => true,
		  // This array of options controls the labels displayed in the WordPress Admin UI
		  'labels' => array(
			'name' => _x( 'Subjects', 'taxonomy general name','default' ),
			'singular_name' => _x( 'Subject', 'taxonomy singular name','default' ),
			'search_items' =>  __( 'Search Subjects' ,'default'),
			'all_items' => __( 'All Subjects' ,'default'),
			'parent_item' => __( 'Parent Subject','default' ),
			'parent_item_colon' => __( 'Parent Subject:' ,'default'),
			'edit_item' => __( 'Edit Subject' ,'default'),
			'update_item' => __( 'Update Subject' ,'default'),
			'add_new_item' => __( 'Add New Subject','default' ),
			'new_item_name' => __( 'New Subject Name' ,'default'),
			'menu_name' => __( 'Subjects','default' ),
		  ),
		  // Control the slugs used for this taxonomy
		  'rewrite' => array(
			'slug' => 'subjects', // This controls the base slug that will display before each term
			'with_front' => false, // Don't display the category base before "/locations/"
			'hierarchical' => true // This will allow URL's like "/locations/boston/cambridge/"
		  ),
		));
	  }
	add_action( 'init', 'add_custom_taxonomies', 0 );

	  function set_theopenbook_settings() {
		// Add tag metabox to book
		register_taxonomy_for_object_type('subject', 'book');
		register_taxonomy_for_object_type('post_tag', 'book');
		// Add category metabox to page
	//}
	add_action( 'init', 'set_theopenbook_settings' );
}


?>