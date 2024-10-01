<?php
/*
* Options page for terminek plugin
*/

if (!defined('ABSPATH')) die('No direct access allowed');

/*
 * Add page to the wp admin menu
 *
 * @date  27/01/21
 * @since 1.0.0
 *
 */
 function add_tek_admin_page(){
    add_options_page( 'Event options', 'Event options', 'manage_options', 'tek-opt', 'tek_display_admin_page' );
  }
add_action('admin_menu', 'add_tek_admin_page');


/*
 * Add sections, fields, settings during api init
 *
 * @date  27/01/21
 * @since 1.0.0
 *
 */
function tek_settings_api_init() {
  // Add the section to label settings 
  add_settings_section(
    'tek_label_section',
    __('Labels', 'tek'),
    'tek_label_section_callback',
    'tek-opt'
  );
  // fields for label section
  add_settings_field(
    'tek_label_dates',
    __('Label "Dates"', 'tek'),
    'tek_label_dates_callback',
    'tek-opt',
    'tek_label_section'
  );
  add_settings_field(
    'tek_label_start',
    __('Label "Start"', 'tek'),
    'tek_label_start_callback',
    'tek-opt',
    'tek_label_section'
  );
  add_settings_field(
    'tek_label_end',
    __('Label "End"', 'tek'),
    'tek_label_end_callback',
    'tek-opt',
    'tek_label_section'
  );
  add_settings_field(
    'tek_show_location',
    __('Show location', 'tek'),
    'tek_show_location_callback',
    'tek-opt',
    'tek_label_section'
  );
  add_settings_field(
    'tek_label_location',
    __('Label "Location"', 'tek'),
    'tek_label_location_callback',
    'tek-opt',
    'tek_label_section'
  );
  add_settings_field(
    'tek_show_custom',
    __('Show custom input', 'tek'),
    'tek_show_custom_callback',
    'tek-opt',
    'tek_label_section'
  );
  add_settings_field(
    'tek_admin_label_custom',
    __('Label "Custom" for admin area', 'tek'),
    'tek_admin_label_custom_callback',
    'tek-opt',
    'tek_label_section'
  );
  add_settings_field(
    'tek_label_custom',
    __('Label "Custom" for frontend', 'tek'),
    'tek_label_custom_callback',
    'tek-opt',
    'tek_label_section'
  );

//single event settings section
 add_settings_section(
    'tek_single_section',
    __('Date list on single event pages', 'tek'),
    'tek_single_section_callback',
    'tek-opt'
  );
//single event settings fields
  add_settings_field(
    'tek_single_head',
    __('Headline format of date list', 'tek'),
    'tek_single_head_callback',
    'tek-opt',
    'tek_single_section'
  );
  add_settings_field(
    'tek_single_wrapper',
    __('Wrapper for date list', 'tek'),
    'tek_single_wrapper_callback',
    'tek-opt',
    'tek_single_section'
  );
  add_settings_field(
    'tek_single_past',
    __('Hide past dates', 'tek'),
    'tek_single_past_callback',
    'tek-opt',
    'tek_single_section'
  );
  add_settings_field(
    'tek_single_auto',
    __('Hide automatic display of date list', 'tek'),
    'tek_single_auto_callback',
    'tek-opt',
    'tek_single_section'
  );

//event archive settings section
 add_settings_section(
    'tek_archive_section',
    __('Display options of event archives', 'tek'),
    'tek_archive_section_callback',
    'tek-opt'
  );
//event archive settings fields
  add_settings_field(
    'tek_archive_order',
    __('Order of display', 'tek'),
    'tek_archive_order_callback',
    'tek-opt',
    'tek_archive_section'
  );
  add_settings_field(
    'tek_archive_head',
    __('Headline format of event archive entries', 'tek'),
    'tek_archive_head_callback',
    'tek-opt',
    'tek_archive_section'
  );
  add_settings_field(
    'tek_archive_thumbs',
    __('Hide post thumbnails', 'tek'),
    'tek_archive_thumbs_callback',
    'tek-opt',
    'tek_archive_section'
  );
  add_settings_field(
    'tek_archive_excerpt',
    __('Hide excerpts', 'tek'),
    'tek_archive_excerpt_callback',
    'tek-opt',
    'tek_archive_section'
  );
  add_settings_field(
    'tek_archive_link',
    __('Display readmore link', 'tek'),
    'tek_archive_link_callback',
    'tek-opt',
    'tek_archive_section'
  );
  add_settings_field(
    'tek_archive_linktext',
    __('Text readmore link', 'tek'),
    'tek_archive_linktext_callback',
    'tek-opt',
    'tek_archive_section'
  );
//format of date list headline on single event (select)
//hide dates in the past on single event page (check)
//title format in archive (select)
//show post thumbnails in archive (check)
//show excerpt in archive (check)


  // Register our setting so that $_POST handling is done for us and
  // our callback function just has to echo the <input>
  register_setting( 'tek-opt', 'tek_label_dates' );
  register_setting( 'tek-opt', 'tek_label_start' );
  register_setting( 'tek-opt', 'tek_label_end' );
  register_setting( 'tek-opt', 'tek_show_location' );
  register_setting( 'tek-opt', 'tek_label_location' );
  register_setting( 'tek-opt', 'tek_show_custom' );
  register_setting( 'tek-opt', 'tek_label_custom' );
  register_setting( 'tek-opt', 'tek_admin_label_custom' );

  register_setting( 'tek-opt', 'tek_single_head' );
  register_setting( 'tek-opt', 'tek_single_wrapper' );
  register_setting( 'tek-opt', 'tek_single_past' );
  register_setting( 'tek-opt', 'tek_single_auto' );

  register_setting( 'tek-opt', 'tek_archive_order' );
  register_setting( 'tek-opt', 'tek_archive_head' );
  register_setting( 'tek-opt', 'tek_archive_thumbs' );
  register_setting( 'tek-opt', 'tek_archive_excerpt' );
  register_setting( 'tek-opt', 'tek_archive_link' );
  register_setting( 'tek-opt', 'tek_archive_linktext' );

}
add_action( 'admin_init', 'tek_settings_api_init' );



/*
 * Callback functions for label section
 *
 * @date  27/01/21
 * @since 1.0.0
 *
 */
function tek_label_section_callback() {
  //global $terminek;
  echo '<p>' . __('Insert your custom labels for the display of event dates on single event pages and archives. ', 'tek') . __('On your event pages the date list will look more or less like this: ', 'tek') .'</p>';

  echo '<div class="tek_example_box">';
  echo  tek_format_date_list(array(
        array(
          'start' => date_i18n(get_option('date_format'), strtotime('today')),
          'end' => date_i18n(get_option('date_format'), strtotime('tomorrow')),
          'raw_start' => strtotime('today'),
          'raw_end' => '20230517',
          'location' => 'Berlin',
          'custom' => 'Dr. Schuster'
        ),
         array(
          'start' => date_i18n(get_option('date_format'), strtotime('03-10-2025')),
          'end' => date_i18n(get_option('date_format'), strtotime('05-10-2025')),
          'raw_start' => strtotime('03-10-2025'),
          'raw_end' => '20231005',
          'location' => 'Potsdam',
          'custom' => 'Dr. Schuster'
        ),
  ));
  echo '</div>';

 }
function tek_label_dates_callback() {
  global $terminek;
  echo '<input name="tek_label_dates" id="tek_label_dates" type="text" value="' . $terminek->tek_get_option( 'tek_label_dates' ) . '"/>';
}
function tek_label_start_callback() {
  global $terminek;
  echo '<input name="tek_label_start" id="tek_label_start" type="text" value="' . $terminek->tek_get_option( 'tek_label_start' ) . '"/>';
}
function tek_label_end_callback() {
  global $terminek;
  echo '<input name="tek_label_end" id="tek_label_end" type="text" value="' . $terminek->tek_get_option( 'tek_label_end' ) . '"/>';
}
function tek_show_location_callback() {
   global $terminek;
  $selected = $terminek->tek_get_option( 'tek_show_location' );

  if($selected == 'show'){
      echo '<input name="tek_show_location" id="tek_show_location" type="checkbox" checked="checked" value="show"/>';
  }else{
      echo '<input name="tek_show_location" id="tek_show_location" type="checkbox" value="show"/>';
  }
}
function tek_label_location_callback() {
  global $terminek;
  echo '<input name="tek_label_location" id="tek_label_location" type="text" value="' . $terminek->tek_get_option( 'tek_label_location' ) . '"/>';
}
function tek_show_custom_callback() {
   global $terminek;
  $selected = $terminek->tek_get_option( 'tek_show_custom' );

  if($selected == 'show'){
      echo '<input name="tek_show_custom" id="tek_show_custom" type="checkbox" checked="checked" value="show"/>';
  }else{
      echo '<input name="tek_show_custom" id="tek_show_custom" type="checkbox" value="show"/>';
  }
}
function tek_admin_label_custom_callback() {
  global $terminek;
  echo '<input name="tek_admin_label_custom" id="tek_admin_label_custom" type="text" value="' . $terminek->tek_get_option( 'tek_admin_label_custom' ) . '"/>';
}
function tek_label_custom_callback() {
  global $terminek;
  echo '<input name="tek_label_custom" id="tek_label_custom" type="text" value="' . $terminek->tek_get_option( 'tek_label_custom' ) . '"/>';
}



/*
 * Callback functions for single event section
 *
 * @date  27/01/21
 * @since 1.0.0
 *
 */
function tek_single_section_callback() {
  echo '<p>' . __('A list of all dates of an event will be added automatically to the single event pages.', 'tek') . '</p>';
 }

function tek_single_wrapper_callback() {
  global $terminek;
  $former_value = $terminek->tek_get_option( 'tek_single_wrapper' );
  echo '<select name="tek_single_wrapper" id="tek_single_wrapper" value="' . $former_value . '"/>';
    echo ($former_value == 'span')? '<option value="span" selected="selected">' . __('html span', 'tek') . '</option>' : '<option value="span">' . __('html span', 'tek') . '</option>';
    echo ($former_value == 'li')? '<option value="li" selected="selected">' . __('html list', 'tek') . '</option>' : '<option value="li">' . __('html list', 'tek') . '</option>';
  echo '</select>';
}

function tek_single_head_callback() {
  global $terminek;
  $former_value = $terminek->tek_get_option( 'tek_single_head' );
  echo '<select name="tek_single_head" id="tek_single_head" value="' . $former_value . '"/>';
    echo ($former_value == 'h2')? '<option value="h2" selected="selected">h2</option>' : '<option value="h2">h2</option>';
    echo ($former_value == 'h3')? '<option value="h3" selected="selected">h3</option>' : '<option value="h3">h3</option>';
    echo ($former_value == 'h4')? '<option value="h4" selected="selected">h4</option>' : '<option value="h4">h4</option>';
    echo ($former_value == 'h5')? '<option value="h5" selected="selected">h5</option>' : '<option value="h5">h5</option>';
    echo ($former_value == 'p')? '<option value="p" selected="selected">p</option>' : '<option value="p">p</option>';
  echo '</select>';

}
function tek_single_past_callback() {
  global $terminek;
  $selected = $terminek->tek_get_option( 'tek_single_past' );

  if($selected == 'hide'){
      echo '<input name="tek_single_past" id="tek_single_past" type="checkbox" checked="checked" value="hide"/>';
  }else{
      echo '<input name="tek_single_past" id="tek_single_past" type="checkbox" value="hide"/>';
  } 
}
function tek_single_auto_callback() {
  global $terminek;
  $selected = $terminek->tek_get_option( 'tek_single_auto' );

  if($selected == 'hide'){
      echo '<input name="tek_single_auto" id="tek_single_auto" type="checkbox" checked="checked" value="hide"/>';
  }else{
      echo '<input name="tek_single_auto" id="tek_single_auto" type="checkbox" value="hide"/>';
  } 
}

/*
 * Callback functions for event archive section
 *
 * @date  27/01/21
 * @since 1.0.0
 *
 */
function tek_archive_section_callback() {
  echo '<p>' . __('The event archive can be inserted into pages via the following shortcode: ', 'tek') . '<code>[tek_events]</code>' .
   __('You can add the following options to the shortcode: ', 'tek') . '<code>[tek_events type="insert_event_type_here" time="future"]</code>' . __('Replace "insert_event_type_here" with the slug of one of your event types to show only events by this type. Insert "future" for displaying events that take place today or in the future, "past" for events that took place in the past and "all" to diplay past and future events. "future" is the default value. ', 'tek') .'</p>';
 }

 function tek_archive_order_callback() {
  global $terminek;
  $former_value = $terminek->tek_get_option( 'tek_archive_order' );
  echo '<select name="tek_archive_order" id="tek_archive_order" value="' . $former_value . '"/>';
    echo ($former_value == 'date_title')? '<option value="date_title" selected="selected">' . __('Date, Title', 'tek') . '</option>' : '<option value="date_title">' . __('Date, Title', 'tek') . '</option>';
    echo ($former_value == 'title_date')? '<option value="title_date" selected="selected">' . __('Title, Date', 'tek') . '</option>' : '<option value="title_date">' . __('Title, Date', 'tek') . '</option>';
  echo '</select>';
}

function tek_archive_head_callback() {
  global $terminek;
  $former_value = $terminek->tek_get_option( 'tek_archive_head' );
  echo '<select name="tek_archive_head" id="tek_archive_head" value="' . $former_value . '"/>';
    echo ($former_value == 'h2')? '<option value="h2" selected="selected">h2</option>' : '<option value="h2">h2</option>';
    echo ($former_value == 'h3')? '<option value="h3" selected="selected">h3</option>' : '<option value="h3">h3</option>';
    echo ($former_value == 'h4')? '<option value="h4" selected="selected">h4</option>' : '<option value="h4">h4</option>';
    echo ($former_value == 'h5')? '<option value="h5" selected="selected">h5</option>' : '<option value="h5">h5</option>';
    echo ($former_value == 'p')? '<option value="p" selected="selected">p</option>' : '<option value="p">p</option>';
  echo '</select>';
}

function tek_archive_thumbs_callback() {
  global $terminek;
  $selected = $terminek->tek_get_option( 'tek_archive_thumbs' );

  if($selected == 'hide'){
      echo '<input name="tek_archive_thumbs" id="tek_archive_thumbs" type="checkbox" checked="checked" value="hide"/>';
  }else{
      echo '<input name="tek_archive_thumbs" id="tek_archive_thumbs" type="checkbox" value="hide"/>';
  }
}
function tek_archive_excerpt_callback() {
  global $terminek;
  $selected = $terminek->tek_get_option( 'tek_archive_excerpt' );

  if($selected == 'hide'){
      echo '<input name="tek_archive_excerpt" id="tek_archive_excerpt" type="checkbox" checked="checked" value="hide"/>';
  }else{
      echo '<input name="tek_archive_excerpt" id="tek_archive_excerpt" type="checkbox" value="hide"/>';
  }
}
function tek_archive_link_callback() {
  global $terminek;
  $selected = $terminek->tek_get_option( 'tek_archive_link' );

  if($selected == 'show'){
      echo '<input name="tek_archive_link" id="tek_archive_link" type="checkbox" checked="checked" value="show"/>';
  }else{
      echo '<input name="tek_archive_link" id="tek_archive_link" type="checkbox" value="show"/>';
  }
}
function tek_archive_linktext_callback() {
  global $terminek;
  echo '<input name="tek_archive_linktext" id="tek_archive_linktext" type="text" value="' . $terminek->tek_get_option( 'tek_archive_linktext' ) . '"/>';
}


/*
 * Display the admin page
 *
 * @date  27/01/21
 * @since 1.0.0
 *
 */
function tek_display_admin_page(){
    
    if ( !current_user_can( 'manage_options' ) )  {
      wp_die( __( 'You do not have sufficient permissions to access this page.', 'tek' ) );
    }
  
      // add error/update messages

     // check if the user have submitted the settings
     // wordpress will add the "settings-updated" $_GET parameter to the url
     if ( isset( $_GET['settings-updated'] ) ) {
     // add settings saved message with the class of "updated"
     add_settings_error( 'tek_opt_messages', 'tek_opt_message', __( 'Settings Saved', 'tek' ), 'updated' );
     }

     // show error/update messages
     settings_errors( 'tek_opt_messages' );

  ?>
    <div class="wrap">
    <h1><?php _e('TerminEK Event Options', 'tek') ?></h1>
  
    <form id="form-tek-options" action="options.php" method="post">
        <?php
            settings_fields( 'tek-opt' );
            do_settings_sections( 'tek-opt' );
            submit_button( 'Save Settings' );
        ?>
    </form>
   </div>
  <?php
  
  }





 