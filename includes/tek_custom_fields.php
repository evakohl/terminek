<?php
/*
* Adding the date fields to the tek_event post type for admin 
*/

if (!defined('ABSPATH')) die('No direct access allowed');



/*
 * Add meta box to tek event edit page
 *
 * @date  13/07/20
 * @since 1.0.0
 *
 * @param void
 * @return  void
 */
function tek_add_meta_box() {

  add_meta_box(
    'tek_data_box',
    __('Event dates','tek'),
    'tek_add_admin_fields',
    'tek_event',
    'side',
    'high' );
}
add_action( 'add_meta_boxes', 'tek_add_meta_box' );

/*
 * Add js to meta box 
 *
 * @date  04/08/20
 * @since 1.0.0
 *
 * @param void
 * @return  void
 */
function tek_admin_scripts()
{

   global $terminek;
  $label_c = ($terminek->tek_get_option( 'tek_admin_label_custom' ))? $terminek->tek_get_option( 'tek_admin_label_custom' ) : 'Custom input';

    // get current admin screen, or null
    $screen = get_current_screen();
    // verify admin screen object
    if (is_object($screen)) {
        // enqueue only for specific post types
        if (in_array($screen->post_type, ['tek_event'])) {
            // enqueue script
            //wp_enqueue_script('tek_admin_meta_box_script', $terminek->tek_url . 'js/tek-admin-cf.js', ['jquery']);
            wp_enqueue_script('tek_admin_meta_box_script', $terminek->tek_get_info( 'tek_url' ) . 'js/tek-admin-cf.js', ['jquery']);
            //localize script, create a custom js object
            wp_localize_script(
                'tek_admin_meta_box_script',
                'tek_admin_script',
                [
                    'url' => admin_url('admin-ajax.php'),
                    'showloc' =>  ($terminek->tek_get_option( 'tek_show_location' ) == 'show'),
                    'showcust' =>  ($terminek->tek_get_option( 'tek_show_custom' ) == 'show'),
                    'labelcust' => $label_c
                ]
            );
        }
    }
}
// enqueue scripts
add_action('admin_enqueue_scripts', 'tek_admin_scripts');

/*
 * Add input fields to tek event meta box.
 *
 * @date  13/07/20
 * @since 1.0.0
 *
 * @param WP_Post $post Post Object
 * @return  void
 */
function tek_add_admin_fields($post) {

    global $terminek;

    // Add an nonce field so we can check for it later.
    wp_nonce_field( 'tek_event_dates', 'tek_event_dates_nonce' );

    // Use get_post_meta to retrieve an existing date from the database.
    $dates = get_post_meta(  $post->ID, '_tek_startend_date', false );

    // Format date output according to wp option
    $dates = tek_format_dates($dates);

    // Display the form, using the current value.
    ?>
      <div class="tek_date_container" id="tek_date_container">
        <p>
          <?php 
          _e( 'Please use the following formatting for dates: DD.MM.YYYY or DD-MM-YYYY.', 'tek' );
          ?>
           
        </p>

         
    <?php

    $counter = 0;

    do {
      $start = (isset($dates[$counter]['start']))? $dates[$counter]['start'] : '';
      $end = (isset($dates[$counter]['end']))? $dates[$counter]['end'] : '';
      $location = (isset($dates[$counter]['location']))? $dates[$counter]['location'] : '';
      $custom = (isset($dates[$counter]['custom']))? $dates[$counter]['custom'] : '';
      ?>
        <div class="tek_date_box tek_date_box_<?php echo $counter; ?>">
          <label for="tek_start_date_<?php echo $counter; ?>">
              <?php _e( 'Start date', 'tek' ); ?>
          </label>
          <input type="text" class="tek_date_input" id="tek_start_date_<?php echo $counter; ?>" name="tek_start_date_<?php echo $counter; ?>" value="<?php echo esc_html( $start ); ?>" size="25" />
          <br>
           <label for="tek_end_date_<?php echo $counter; ?>">
              <?php _e( 'End date', 'tek' ); ?>
          </label>
          <input type="text" class="tek_date_input" id="tek_end_date_<?php echo $counter; ?>" name="tek_end_date_<?php echo $counter; ?>" value="<?php echo esc_html( $end ); ?>" size="25" />

          <?php if( $terminek->tek_get_option( 'tek_show_location' ) == 'show' ){ ?>
            <br>
            <label for="tek_location_<?php echo $counter; ?>">
              <?php _e( 'Location', 'tek' ); ?>
            </label>
            <input type="text" class="tek_loc_input" id="tek_location_<?php echo $counter; ?>" name="tek_location_<?php echo $counter; ?>" value="<?php echo esc_html( $location ); ?>" size="25" />
          <?php } ?>

          <?php if( $terminek->tek_get_option( 'tek_show_custom' ) == 'show' ){ ?>
            <br>
            <label for="tek_custom_<?php echo $counter; ?>">
              <?php 
              if($terminek->tek_get_option( 'tek_admin_label_custom' )){
                _e( $terminek->tek_get_option( 'tek_admin_label_custom' ), 'tek' ); 
              }else{
                _e( 'Custom input', 'tek' ); 
              }
              
              ?>
            </label>
            <input type="text" class="tek_cust_input" id="tek_custom_<?php echo $counter; ?>" name="tek_custom_<?php echo $counter; ?>" value="<?php echo esc_html( $custom ); ?>" size="25" />
          <?php } ?>

        </div>

      
      <?php
      $counter++;
    } while( isset($dates[$counter]) );

    ?>
        <div class="tek_button_box">
            <button type="button" id="tek_add_date" name="tek_add_date"><?php _e('+ Add date', 'tek' ); ?></button>
        </div>
        <input type="hidden" id="tek_changed" name="tek_changed" value="false" />
      </div>

    <?php
}


/*
 * Save meta box content to tek event edit page
 *
 * @date  13/07/20
 * @since 1.0.0
 *
 * @param int $post_id
 * @return  void
 */
function tek_save_meta_box($post_id) {

  global $terminek;
  $show_location = ($terminek->tek_get_option( 'tek_show_location' ) == 'show');
  $show_custom = ($terminek->tek_get_option( 'tek_show_custom' ) == 'show');
  /*
     * We need to verify this came from the our screen and with proper authorization,
     * because save_post can be triggered at other times.
     */

    // Check if our nonce is set.
    if ( ! isset( $_POST['tek_event_dates_nonce'] ) ) {
        return $post_id;
    }

    $nonce = $_POST['tek_event_dates_nonce'];

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $nonce, 'tek_event_dates' ) ) {
        return $post_id;
    }

    // check if there was a multisite switch before
    if ( is_multisite() && ms_is_switched() ) {
        return $post_id;
    }

    /*
     * If this is an autosave, our form has not been submitted,
     * so we don't want to do anything.
     */
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return $post_id;
    }

    // Check the user's permissions.
    if ( 'page' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_page', $post_id ) ) {
            return $post_id;
        }
    } else {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }
    }

    /* OK, it's safe for us to save the data now. */
    // Sanitize the user input.
    $counter = 0;
    $dates = array();

    // Get all the dates, convert them to standard format and put them into the array
    do {
      $sd = sanitize_text_field( $_POST['tek_start_date_'.$counter] );
      $ed = sanitize_text_field( $_POST['tek_end_date_'.$counter] );
      $loc = sanitize_text_field( $_POST['tek_location_'.$counter] );
      $cust = sanitize_text_field( $_POST['tek_custom_'.$counter] );


      if(!empty($sd) && strtotime($sd)){
        // start date exists and ist valid

        if(!empty($ed) && strtotime($ed)){
        // end date exists and is valid
          $date = array(
            'startdate' => date_i18n('Ymd', strtotime($sd)),
            'enddate' =>  date_i18n('Ymd', strtotime($ed)),
          );
        }else{
          // only save start date else
          $date = array(
            'startdate' => date_i18n('Ymd', strtotime($sd)),
            'enddate' =>  null,
          );
        }

        if( $show_location ){
          $date['location'] = $loc; 
        }

        if( $show_custom ){
          $date['custom'] = $cust; 
        }

        $dates[] = $date;

      }

      $counter++;

    } while( isset($_POST['tek_start_date_'.$counter]) );

    // sort dates
    array_multisort(array_column($dates, 'startdate'), SORT_ASC, $dates);

    // delete previous dates
    delete_post_meta( $post_id, '_tek_start_date');
    delete_post_meta( $post_id, '_tek_startend_date');


    foreach( $dates as $date) {
      // save dates 

      $date['startenddate'] = array(
        'start' => $date['startdate'],
        'end' => $date['enddate']
      );

      if( $show_location ){
          $date['startenddate']['location'] = $date['location']; 
        }

      if( $show_custom ){
          $date['startenddate']['custom'] = $date['custom']; 
      }

      // add dates as custom fields
      add_post_meta( $post_id, '_tek_start_date', $date['startdate'] );
      add_post_meta( $post_id, '_tek_startend_date', wp_slash(json_encode($date['startenddate'])) );
      
    }
  
}

add_action( 'save_post', 'tek_save_meta_box' );


//add_action('edit_post', 'tek_add_admin_fields');


/*
 * Format and decode startend custom field data.
 *
 * @date  10/08/20
 * @since 1.0.0
 *
 * @param Array $dates Array of json formatted dates
 * @return  Array of formatted start- and end-dates
 */
function tek_format_dates($dates) {
  $formatted_dates = array();
   
  if(is_admin()){
    // format to php readable format in backend
    if(get_locale() == 'de_DE'){
      $format = 'd.m.Y'; 
    }else{
      $format = 'd-m-Y'; 
    }
  }else{
    // use wordpress date format for frontend
    $format = get_option('date_format');
  }
  
  
  if( ! empty($dates) ){
    foreach ( $dates as $date ){
      $decoded = json_decode($date);
      $formatted = array(
        'start' => '',
        'end' => '',
        'raw_start' => '',
        'raw_end' => '',
        'location' => '',
        'custom' => ''
      );


      if($decoded->start && strtotime($decoded->start)){
        $formatted['start'] = date_i18n($format, strtotime($decoded->start));
        $formatted['raw_start'] = $decoded->start;

        if($decoded->end && strtotime($decoded->end)){
          $formatted['end'] = date_i18n($format, strtotime($decoded->end));
          $formatted['raw_end'] = $decoded->end;
        }
        if(isset($decoded->location)){
          $formatted['location'] = $decoded->location;
        }
        if(isset($decoded->custom)){
          $formatted['custom'] = $decoded->custom;
        }

      }

      $formatted_dates[] = $formatted;

    }
  }
  return $formatted_dates;

}




