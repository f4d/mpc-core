<?php
class NotificationPost {
	public function __construct() {
    add_action( 'init', array( $this, 'register_custom_post_type' ) );  
    add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) ); 
    add_action( 'save_post', array( $this, 'save_meta_boxes' ) );
	}
	/**
 	* Registers a Custom Post Type called Notification
 	*/
	function register_custom_post_type() {
    register_post_type( 'notification', array(
        'labels' => array(
            'name'               => _x( 'Notifications', 'post type general name', 'mpc-core' ),
            'singular_name'      => _x( 'Notification', 'post type singular name', 'mpc-core' ),
            'menu_name'          => _x( 'Notifications', 'admin menu', 'mpc-core' ),
            'name_admin_bar'     => _x( 'Notification', 'add new on admin bar', 'mpc-core' ),
            'add_new'            => _x( 'Add New', 'notification', 'mpc-core' ),
            'add_new_item'       => __( 'Add New Notification', 'mpc-core' ),
            'new_item'           => __( 'New Notification', 'mpc-core' ),
            'edit_item'          => __( 'Edit Notification', 'mpc-core' ),
            'view_item'          => __( 'View Notification', 'mpc-core' ),
            'all_items'          => __( 'All Notifications', 'mpc-core' ),
            'search_items'       => __( 'Search Notifications', 'mpc-core' ),
            'parent_item_colon'  => __( 'Parent Notifications:', 'mpc-core' ),
            'not_found'          => __( 'No notification found.', 'mpc-core' ),
            'not_found_in_trash' => __( 'No notification found in Trash.', 'mpc-core' ),
        ),
         
        // Frontend
        'has_archive'        => false,
        'public'             => false,
        'publicly_queryable' => false,
         
        // Admin
        'capability_type' => 'post',
        'menu_icon'     => 'dashicons-businessman',
        'menu_position' => 10,
        'query_var'     => true,
        'show_in_menu'  => true,
        'show_ui'       => true,
        'supports'      => array(
            'title',
            'author',
            'comments', 
        ),
    ) );    
	}	
	/**
	* Registers a Meta Box on our Contact Custom Post Type, called 'Contact Details'
	*/
	function register_meta_boxes() {
    add_meta_box( 'notification-text', 
    	'Custom Notication', 
    	array( $this, 'output_meta_box' ), 
    	'notification', 'normal', 'high' );   
	}
	function output_meta_box($post) {
		$notification_text = esc_attr(get_post_meta( $post->ID, '_notification_text', true ));
    // Output label and field
    $str = '<label for="notification_text">' . __( 'Notification Text', 'mpc-core' ) . '</label><br>';
    $str .= '<textarea name="notification_text" id="notification_text" rows="6" cols="60">';
    echo (  $str . $notification_text . '</textarea>'  );
	}
	/**
	* Saves the meta box field data
	*
	* @param int $post_id Post ID
	*/
	function save_meta_boxes( $post_id ) {
      // Check this is the Notification Custom Post Type
      if ( 'notification' == $_GET['post_type']) {
        return $post_id;
      }
      // Check this is the Notification Custom Post Type
	  if ( 'notification' != $_POST['post_type']) {
	    return $post_id;
	  }
	  // Check the logged in user has permission to edit this post
	  if ( ! current_user_can( 'edit_post', $post_id ) ) {
	    return $post_id;
	  }
	  // OK to save meta data
	  $n = sanitize_text_field( $_POST['notification_text'] );
	  update_post_meta( $post_id, '_notification_text', $n );	     
	}	
}