<?php
/*
Plugin Name: TerminEK
Description: Manage and display events in wordpress
Version: 1.1.1
Author: Eva Kohl
Author URI: https://evakohl.de
Text Domain: tek
Domain Path: /lang
*/

if (!defined('ABSPATH')) die('No direct access allowed');

// Custom Post Type 
// Custom Taxonomy 
// Add Custom Fields
// Get event data
// Display Overview (shortcode)
// Display date on details page (shortcode)
// Options page

if( ! class_exists('TerminEK') ) :

class TerminEK {
  
  /** @var string The plugin version number. */
  var $version = '1.1.1';
  
  /** @var array The plugin settings array. */
  var $settings = array();
  
  /** @var array The plugin data array. */
  //var $data = array();
  
  /** @var array Storage for class instances. */
  //var $instances = array();
  
  /**
   * __construct
   *
   * A dummy constructor to ensure TerminEK is only setup once.
   *
   * @date  29/06/20
   * @since 1.0.0
   *
   * @param void
   * @return  void
   */ 
  function __construct() {
    // Do nothing.
  }



  /**
   * initialize
   *
   * Sets up the TerminEK plugin.
   *
   * @date  29/06/20
   * @since 1.0.0
   *
   * @param void
   * @return  void
   */
  function initialize() {
    
    // Define constants.
    $this->tek_define( 'TEK', true );
    $this->tek_define( 'TEK_PATH', plugin_dir_path( __FILE__ ) );
    $this->tek_define( 'TEK_URL', plugin_dir_url( __FILE__ ) );
    $this->tek_define( 'TEK_BASENAME', plugin_basename( __FILE__ ) );
    $this->tek_define( 'TEK_VERSION', $this->version );
    //$this->define( 'TEK_MAJOR_VERSION', 1 );
    
    // Define default settings.
    $this->settings = array(
      'name'            => __('TerminEK', 'tek'),
      'slug'            => dirname( TEK_BASENAME ),
      'version'         => TEK_VERSION,
      'capability'        => 'manage_options',
      'tek_label_dates' => __('Dates', 'tek'),
      'tek_label_start' => __('Start: ', 'tek'),
      'tek_label_end' => __('End: ', 'tek'),
      'tek_single_head' => 'h4',
      'tek_single_past' => '',
      'tek_archive_head' => 'h2',
      'tek_archive_thumbs' => '',
      'tek_archive_excerpt' => 'hide',
      'default_time' => 'future',
    );
    
    // Include additional functions.
    include_once( TEK_PATH . 'includes/tek_utility_functions.php');
    include_once( TEK_PATH . 'includes/tek_custom_fields.php');
    include_once( TEK_PATH . 'includes/tek_frontend.php');
    include_once( TEK_PATH . 'includes/tek_admin_page.php');

    // Add actions: Register Post type and taxonomy
    add_action( 'init', array($this, 'register_tek_post_type'), 20 );
    add_action( 'init', array($this, 'register_tek_eventtype_taxonomy'), 20 );

    // Enqueue styles
    add_action('admin_enqueue_scripts', array($this, 'tek_admin_style'));
  }

 /**
   * Enqueues amin style sheet 
   *
   * @date  12/8/20
   * @since 1.0.0
   *
   * @return  void
   */
function tek_admin_style() {
  wp_enqueue_style('admin-styles',  TEK_URL .'/styles/admin.css');
}

  /**
   * define
   *
   * Defines a constant if doesnt already exist.
   *
   * @date  29/6/20
   * @since 1.0.0
   *
   * @param string $name The constant name.
   * @param mixed $value The constant value.
   * @return  void
   */
  function tek_define( $name, $value = true ) {
    if( !defined($name) ) {
      define( $name, $value );
    }
  }

  /**
   * get_default_setting
   *
   * Retieves a default settings value by its key.
   *
   * @date  29/06/20
   * @since 1.0.0
   *
   * @param String $key Name of setting to retrieve.
   * @return  mixed
   */  
  function tek_get_setting(String $key) {
    return isset($this->settings[ $key ]) ? $this->settings[ $key ] : null;
  } 

    /**
   * get_setting
   *
   * Retieves an option or the default settings value by its key.
   *
   * @date  29/06/20
   * @since 1.0.0
   *
   * @param String $key Name of setting to retrieve.
   * @return  mixed
   */  
  function tek_get_option(String $key) { 
    return (null !== get_option( $key ))? get_option( $key ) : $this->tek_get_setting( $key );
  } 


  /**
   * register_tek_post_type
   *
   * Registers the Event Post Type.
   *
   * @date  29/06/20
   * @since 1.0.0
   *
   * @param void
   * @return  void
   */  
  function register_tek_post_type() {
    //$cap = tek_get_setting('capability');

    $labels  = array(
        'name'                     => __( 'Events' , 'tek' ),
        'singular_name'            => __( 'Event' , 'tek' ),
        'add_new'                  => __( 'Add New' , 'tek' ),
        'add_new_item'             => __( 'Add New Event' , 'tek' ),
        'edit_item'                => __( 'Edit Event' , 'tek' ),
        'new_item'                 => __( 'New Event' , 'tek' ),
        'view_item'                => __( 'View Event' , 'tek' ),
        'view_items'               => __( 'View Events' , 'tek' ),
        'search_items'             => __( 'Search Events' , 'tek' ),
        'not_found'                => __( 'No events found.' , 'tek' ),
        'not_found_in_trash'       => __( 'No events found in Trash.' , 'tek' ),
        'all_items'                => __( 'All Events' , 'tek' ),
        'archives'                 => __( 'Event Archives' , 'tek' ),
        'attributes'               => __( 'Event Attributes' , 'tek' ),
        'insert_into_item'         => __( 'Insert into event' , 'tek' ),
        'uploaded_to_this_item'    => __( 'Uploaded to this event' , 'tek' ),
        'filter_items_list'        => __( 'Filter events list' , 'tek' ),
        'items_list_navigation'    => __( 'Events list navigation' , 'tek' ),
        'items_list'               => __( 'Events list' , 'tek' ),
        'item_published'           => __( 'Event published' , 'tek' ),
        'item_published_privately' => __( 'Event published privately' , 'tek' ),
        'item_reverted_to_draft'   => __( 'Event reverted to draft.' , 'tek'), 
        'item_scheduled'           => __( 'Event scheduled.' ), 
        'item_updated'             => __( 'Event updated.' ),
    );

    register_post_type( 'tek_event',
      array(
        'labels' => $labels,
        'public' => true,
        //'taxonomies' => array('tek_eventtype'),
        'has_archive' => true,
        'show_ui' => true,
        'exclude_from_search' => false,
        'hierarchical' => false,
        'query_var' => true,
        'show_in_nav_menus' => true,
        'supports' => array( 'title', 'editor', 'taxonomies', 'revisions', 'author', 'excerpt', 'thumbnail'),
        'publicly_queryable' => true,
        'menu_icon' => 'dashicons-calendar-alt',
        'menu_position' => 21,
        'rewrite' => array( 'slug' => __('event', 'tek'), 'pages' => true ),
        //'capability_type' => array('tek_event', 'tek_events'),
        'capability_type' => 'post',
        /*'capabilities'    => array(
          'edit_post'     => $cap,
          'delete_post'   => $cap,
          'edit_posts'    => $cap,
          'delete_posts'    => $cap,
        ), */
        //'map_meta_cap' => true,
        'show_in_rest' => true // for Gutenberg Editor to work
      )
    );
  }

  /**
   * register_tek_eventtype_taxonomy
   *
   * Registers the Event type taxonomy.
   *
   * @date  29/06/20
   * @since 1.0.0
   *
   * @param void
   * @return  void
   */  
  function register_tek_eventtype_taxonomy() {

    // Labels ( for GUI )
    $labels = array(
      'name' => _x( 'Event types', 'tek_eventtype', 'tek' ),
      'singular_name' => _x( 'Event type', 'tek_eventtype', 'tek' ),
      'search_items' =>  __( 'Search event types', 'tek' ),
      'popular_items' => __( 'Popular event types', 'tek' ),
      'all_items' => __( 'All event types', 'tek' ),
      'parent_item' =>  __( 'Parent event type', 'tek' ),
      'parent_item_colon' =>  __( 'parent event type', 'tek' ),
      'edit_item' => __( 'Edit event type', 'tek' ), 
      'update_item' =>  __( 'Update event type', 'tek' ),
      'add_new_item' =>  __( 'Add new event type', 'tek' ),
      'new_item_name' =>  __( 'New name of event type', 'tek' ),
      'separate_items_with_commas' =>  __( 'Separate event types with commas', 'tek' ),
      'add_or_remove_items' =>  __( 'Add or remove event types', 'tek' ),
      'choose_from_most_used' =>  __( 'Choose from most used event types', 'tek' ),
      'menu_name' =>  __( 'Event types', 'tek' ),
    ); 

    // Now register the taxonomy
    register_taxonomy('tek_eventtype','tek_event',array(
      'hierarchical' => true,
      'labels' => $labels,
      'show_ui' => true,
      'show_admin_column' => true,
      'update_count_callback' => '_update_post_term_count',
      'query_var' => true,
      'rewrite' => array( 'slug' => __('eventtype', 'tek') ),
      'show_in_rest' => true // for Gutenberg Editor to work
    ));
    
    register_taxonomy_for_object_type('tek_eventtype', 'tek_event');
    
    // insert terms
    wp_insert_term( _x( 'General', 'tek_eventtype', 'tek' ), 'tek_eventtype', $args = array(
      'slug' => _x( 'general', 'tek_eventtype', 'tek' ),
      'description' => ''
    ) );
  }



} //end class TerminEK


 

/*
 * terminek
 *
 * The main function. Creates one TerminEK instance.
 *
 * @date  29/06/20
 * @since 1.0.0
 *
 * @param void
 * @return  TerminEK
 */
function terminek() {
  global $terminek;
  
  // Instantiate only once.
  if( !isset($terminek) ) {
    $terminek = new TerminEK();
    $terminek->initialize();
  }
  return $terminek;
}

// Instantiate.
terminek();

endif;
