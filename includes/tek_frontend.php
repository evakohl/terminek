<?php
/*
* Adding the date fields to the tek_event post type on the frontend and displaying the tek_event list
*/

if (!defined('ABSPATH')) die('No direct access allowed');


/*
 * Format date list for events
 *
 * @date  04/10/21
 * @since 1.1.0
 *
 * @param dates Array array of date parameters
 * @param archive_view  bool true to display dates in archive
 * @return  String The formatted date list
 */
function tek_format_date_list ($dates, $archive_view = false) {
    global $terminek;
    $label_dates = $terminek->tek_get_option( 'tek_label_dates' );
    $label_start = $terminek->tek_get_option( 'tek_label_start' );
    $label_end = $terminek->tek_get_option( 'tek_label_end' );
    $show_location = $terminek->tek_get_option( 'tek_show_location' );
    $label_location = $terminek->tek_get_option( 'tek_label_location' );
    $show_custom = $terminek->tek_get_option( 'tek_show_custom' );
    $label_custom = $terminek->tek_get_option( 'tek_label_custom' );
    $show_custom2 = $terminek->tek_get_option( 'tek_show_custom2' );
    $label_custom2 = $terminek->tek_get_option( 'tek_label_custom2' );
    $head_format = $terminek->tek_get_option( 'tek_single_head' );
    $wrapper = ($terminek->tek_get_option( 'tek_single_wrapper' ))? $terminek->tek_get_option( 'tek_single_wrapper' ) : 'span';
    $show_past = $terminek->tek_get_option( 'tek_single_past' );

    $html = '';

    if( !empty($dates) ){

        $html .= '<div class="tek-data tek-data-content">';
        if(!empty($label_dates) && !$archive_view)
            $html .= '<' . $head_format . ' class="tek_title">' . $label_dates . '</' . $head_format . '>';
        // display list of dates
        if( $archive_view ) {
            $html .= '<div class="tek-event-dates">';
        } else {
            $html .= '<ul class="tek-event-dates">';
        }

        foreach($dates as $date) {
            if(( !$archive_view ) && ($show_past == 'hide') && ($date['raw_start'] < current_time( 'Ymd' ))){
                $html .= '';
            }else{
                if( !$archive_view )
                    $html .= '<li>';

                if($wrapper == 'li')
                    $html .= '<ul><li class="tek-list-item-start">';

                $html .= '<span class="tek-label-start">' . $label_start . ' </span>
                        <span class="tek-date-start">' . $date['start'] . ' </span>';

                if($wrapper == 'li')
                    $html .= '</li>';

                if(!empty($date['end'])){
                    if($wrapper == 'li')
                        $html .= '<li class="tek-list-item-end">';
                    $html .= '<span class="tek-label-end">' . $label_end . '</span>
                            <span class="tek-date-end">' . $date['end'] . ' </span>';
                    if($wrapper == 'li')
                        $html .= '</li>';
                    }
                if(isset($show_location) && ($show_location == 'show')){
                    if(!empty($date['location'])){
                        if($wrapper == 'li')
                            $html .= '<li class="tek-list-item-location">';
                        $html .= '<span class="tek-label-location">' . $label_location . '</span>
                            <span class="tek-date-location">' . $date['location'] . ' </span>';
                        } 
                        if($wrapper == 'li')
                            $html .= '</li>';
                    }
              
                if(isset($show_custom) && ($show_custom == 'show')){
                    if(!empty($date['custom'])){
                        if($wrapper == 'li')
                            $html .= '<li class="tek-list-item-custom">';
                        $html .= '<span class="tek-label-custom">' . $label_custom . '</span>
                            <span class="tek-date-custom">' . $date['custom'] . ' </span>';
                        if($wrapper == 'li')
                            $html .= '</li>';
                        } 
                    }

                if(isset($show_custom2) && ($show_custom2 == 'show')){
                    if(!empty($date['custom2'])){
                        if($wrapper == 'li')
                            $html .= '<li  class="tek-list-item-custom">';
                        $html .= '<span class="tek-label-custom">' . $label_custom2 . '</span>
                            <span class="tek-date-custom">' . $date['custom2'] . ' </span>';
                        if($wrapper == 'li')
                            $html .= '</li>';
                        } 
                    }
                
                if($wrapper == 'li')
                    $html .= '</ul>';
                
                if( !$archive_view ) {
                    $html .= '</li>';
                }
                
            }
            
        }
        if( $archive_view ) {
            $html .= '</div>';
        } else {
            $html .= '</ul>';
        }
        $html .= '</div>';
    }
    
    return $html;
}


/*
 * Add date to the post content if it is a tek event
 *
 * @date  13/07/20
 * @since 1.0.0
 *
 * @param String $content the content from the database
 * @return  String The content with the attached dates
 */
function tek_add_date_to_content ( $content ) {
    global $terminek;
    $show_list = $terminek->tek_get_option( 'tek_single_auto' );

    if ( is_main_query() && (get_post_type() == 'tek_event') &&  ($show_list != 'hide')) {
        $dates = tek_get_date_list(get_the_ID());
        $html = tek_format_date_list ($dates);
        return $html . $content;

    }else{
        return $content;
    }
}
add_filter( 'the_content', 'tek_add_date_to_content' );

/*
 * Leave Excerpt like it is, do not add dates
 *
 * @date  26/03/21
 * @since 1.0.0
 *
 * @param String $excerpt the generated excerpt
 * @return  String The excerpt
 */
function tek_not_add_date_to_excerpt ( $excerpt ) {
    // Hier muss noch irgendwas passieren
    return $excerpt;
}
add_filter( 'the_excerpt', 'tek_not_add_date_to_excerpt' );




/*
 * Add shortcode to display event archive in the right order
 *
 * @date  26/01/21
 * @since 1.0.0
 *
 * @param Array $atts the shortcode attributes ('type' for eventtype, 'time' in ['future', 'past', 'all'])
 * @return String the HTML formatted archive
 */
function tek_display_event_archive( $atts ) {
    global $terminek;
    $html = "";
    $events = array();
    $time = (isset($atts['time']))? $atts['time'] : $terminek->tek_get_setting( 'default_time' );
    $head_format = $terminek->tek_get_option( 'tek_archive_head' );
    $show_thumbs = $terminek->tek_get_option( 'tek_archive_thumbs' );
    $show_excerpt = $terminek->tek_get_option( 'tek_archive_excerpt' );

    $order = $terminek->tek_get_option( 'tek_archive_order' );
    $format = get_option('date_format'); 

    $show_link = $terminek->tek_get_option( 'tek_archive_link' );
    $linktext = $terminek->tek_get_option( 'tek_archive_linktext' );

    wp_reset_postdata();

    //query events
    $query_params = array(
      'post_type' => 'tek_event',
      'orderby' => 'title', 
      'order' => 'ASC',
    ) ;

    if(isset($atts['type'])){
        $query_params['tax_query'] = array(
        array(
            'taxonomy' => 'tek_eventtype',
            'field'    => 'slug',
            'terms'    => $atts['type'],
        ),
    );
    }

    $events_query = new WP_Query($query_params);

    //create index with dates
    if ( $events_query->have_posts() ) {
        while ( $events_query->have_posts()) {
            $events_query->the_post();

                $dates = get_post_meta( get_the_ID(), '_tek_startend_date', false );
                foreach($dates as $date){
                    $decoded = json_decode($date);
                    //duplicate events with several dates

                    if( 
                        ($time == 'future') && ($decoded->start >= current_time( 'Ymd' )) ||
                        ($time == 'past') && ($decoded->start <= current_time( 'Ymd' )) ||
                        ($time == 'all')
                        ){
                        $events[] = array(
                            'raw_start' => ($decoded->start)? $decoded->start : '',
                            //'start' => ($decoded->start)? $decoded->start : '',
                            //'end' => ($decoded->end)? $decoded->end : '',
                            'start' => ($decoded->start)? date_i18n($format, strtotime($decoded->start)) : '',
                            'end' => ($decoded->end)? date_i18n($format, strtotime($decoded->end)) : '',
                            'location' => (isset($decoded->location))? stripslashes($decoded->location) : '',
                            'custom' => (isset($decoded->custom))? stripslashes($decoded->custom) : '',
                            'custom2' => (isset($decoded->custom2))? stripslashes($decoded->custom2) : '',
                            'title' => get_the_title(),
                            'permalink' => get_the_permalink(),
                            'excerpt' => get_the_excerpt(),
                            'thumbnail' => get_the_post_thumbnail(),
                            'id' => get_the_ID(),
                        );
                    }

            }

        }
    }
    wp_reset_postdata();

    //sort index by date
    array_multisort(array_column($events, 'raw_start'), SORT_ASC, $events);


    //format event list
    $html .= '<div class="tek-event-archive">';
    foreach($events as $event){
        $html .= '<article id="event-' . $event['id'] . '" class="tek-event-archive-item">';

        // post thumbnail
        if($show_thumbs != 'hide')
            $html .= '<a href="' . $event['permalink'] . '">' . $event['thumbnail'] . '</a>';

        if($order != 'date_title'){
            // show headeline before date
            $html .= '<' . $head_format . '><a href="' . $event['permalink'] . '">' . $event['title'] . '</a></' . $head_format . '>';
        }

        $html .= tek_format_date_list( array($event), true );

        if($order == 'date_title'){
            // show headline after date
            $html .= '<' . $head_format . '><a href="' . $event['permalink'] . '">' . $event['title'] . '</a></' . $head_format . '>';
        }

        if($show_excerpt != 'hide')
            $html .= '<p class="tek-event-excerpt">' . $event['excerpt'] . '</p>';

        if($show_link != 'hide')
            $html .= '<a class="tek-event-link" href="' . $event['permalink'] . '">' . $linktext . '</a>';

        $html .= '</article>';
    }
    $html .= '</div>';

    

    return $html;
}
add_shortcode( 'tek_events', 'tek_display_event_archive' );
// [tek_events type="" time="future"]




/*
 * Get a sorted List of all future dates belonging to one post
 *
 * @date  22/01/21
 * @since 1.0.0
 *
 * @param Int $post_id the content from the database
 * @return  Array nested array with start- and enddates
 */
function tek_get_date_list ( $post_id ) {
    $dates = array();

    if(get_post_meta( $post_id, '_tek_startend_date', false )){
        $dates = get_post_meta( $post_id, '_tek_startend_date', false );
        $dates = tek_format_dates($dates);
        array_multisort(array_column($dates, 'raw_start'), SORT_ASC, $dates);
    }

    return $dates;

}
