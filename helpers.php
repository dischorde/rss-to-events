<?php
    
    // Suggested in WordPress documentation
	defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

    // Initializes and sets up options
    function rssevents_options_init() {
        register_setting( 'rssevents_options', 'rte_saved', 'rte_feedinfo_validate' );
    }

    // Adds the settings page
    function rssevents_setup_menu() {
            add_menu_page( 'RSS Import Options', 'RSS to Events', 'manage_options', 'rss-to-events', 'rte_options_page' );
    }
      
    /**
     *  Checks if The Events Calendar is active. If not, deactivates the plugin and displays notification. 
     *  Returns true if the events calendar is active and false otherwise.
     *  Adapted from here: http://10up.com/blog/2012/wordpress-plug-in-self-deactivation/
     */
    function rte_check_requirements() {
        
            // if the events calendar isn't active
            if ( !is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {
                rte_deactivate();
                add_action( 'admin_notices', 'no_events_calendar_notice' );
    
                function no_events_calendar_notice() {
                    echo '<div class="updated"><p><strong>The Events Calendar</strong> is not active. Therefore, the <strong>RSS to The Events Calendar Importer</strong> has been <strong>deactivated</strong>.</p></div>';
                    if ( isset( $_GET['activate'] ) ) {
                        unset( $_GET['activate'] );
                    }
                }
                
                return false;
            }
        
        // otherwise it must be active so return true
        return true;
    }
    
    /**
     *  Uninstall Hook.
     *  Deletes RSS to Events Importer saved options.
     */
    function rte_rssevents_remove() {
    
        // If uninstall is not called from WordPress, exit
        if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
            exit();
        }
        
        // otherwise, delete rte_saved 
        delete_option( 'rte_saved' );
    }


    /**
     *  Renders the options form. 
     *  Takes in information about the RSS feed to import and settings.   
     *  Displays whether or not auto-import is running. 
     */
    function rte_options_page() {
            
            // render the settings form
            require_once dirname( __FILE__ ) . '/settings_form.php';        
            
            // check if auto import is scheduled
            $importing_scheduled = true;
            if ( wp_next_scheduled('rte_auto_import' ) == false ) {
                $importing_scheduled = false;
            }
                    
            // display status information about auto-import
            echo '<p style="font-size: 14px; font-weight: bold;" >';            
            if ( $importing_scheduled ) {
                echo 'Auto Import is On. The Current Feed is: ' . $options['url'] . '<br>';
            }    
            else {
                echo 'Auto Import is Off. (Ensure required fields are not empty.) ';
            }
            echo '</p>';    
    }
    
    /**
     * Sanitize and validate feed input. Accepts an array, return a sanitized array.
     * Adapted from tutorial here: http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
     */
    function rte_feedinfo_validate($input) {
        // Autoimport must be either unchecked (0) or checked (1)
        $input['autoimport'] = ( $input['autoimport'] == 1 ? 1 : 0 );

        // Go through our options removing any html and 'unset'ing anything empty
        $option_names = array( 'url', 'title', 'description', 'optdescript', 'optlabel', 'stdatetime', 'endatetime', 'locname', 'address', 'guid', 'link' );
        foreach ( $option_names as $opt ) {
            if ( $input[$opt] == '' ) {
                $input[$opt] = null;
            }   
            else {
                $input[$opt] =  wp_filter_nohtml_kses( $input[$opt] );
            }
        }
    
        return $input;
        }
    
    /**
     * Schedule Auto-Update Custom Hook to run daily if Auto-Import is On and feed is valid.
     * Returns true if scheduled, false otherwise.
     */
    function rte_schedule_hook() {
        // get feed options to check if auto-import should run
        $options = get_option( 'rte_saved' );
        
        if ( $options == false ) {
            return false;
        }
        
        // check if import setting is on
        $valid_import = true;
        if ( !isset( $options['autoimport'] ) || $options['autoimport'] == 0 ) {
            $valid_import = false;
        }
        
        // check if any required fields are empty
        else {
            foreach ( array( 'title', 'stdatetime', 'endatetime' ) as $required ) {
                if ( !isset( $options[$required] ) ) {
                    $valid_import = false;
                }
            }
        }
            
        // if the feed is thus valid, schedule auto-import
        if ( $valid_import ) {
            
            // schedule only if not already scheduled
            if ( wp_next_scheduled( 'rte_auto_import' ) == false ) {
                wp_schedule_event( time(), 'daily', 'rte_auto_import' );
                return true;
            }
            
            else {
                // return true to signify (already) scheduled
                return true;
            } 
        }
        
        // if the feed is invalid or auto-import off, unschedule 
        else {
            
            // if scheduled, unschedule
            $timestamp = wp_next_scheduled( 'rte_auto_import' );
            if ( $timestamp != false ) {
                wp_unschedule_event( $timestamp, 'rte_auto_import' );
                return false;
            }
            
            // otherwise just return false (to signify not being scheduled)
            return false;
        }
    }
    
    
    /**
     * Get ID from GUID so can use get_post (to determine if events have been previously imported) 
     * From http://stackoverflow.com/questions/27053807/getting-posts-by-guid
     */
    function get_id_from_guid( $guid ) {
        global $wpdb;
        return $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid=%s", $guid ) );
    }

    /**
     * Parses RSS from the URL specified in options and creates an event for  each item.
     * Ensures GUID field is not empty, giving preference to links
     */
    function rte_create_event( $event_data ) {
    
        // check to ensure event doesn't already exist
        $already_exists = get_post( get_id_from_guid( $event_data['guid'] ) );
        if ( isset( $already_exists ) ) {
            return false;
        }
    
        // convert start and end date and time from string to php date
        $start_date = strtotime( $event_data['stdatetime'] );
        $end_date =  strtotime( $event_data['endatetime'] );
        
        // use explode to parse address assuming the format street address, city, zip
        $address = explode( ', ', $event_data['address'] );
    
        // set up venue array according to tribe_create_venue documentation
        $venue = [ 'Venue' => $event_data['locname'] ];
        if (count($address) == 3) {
            $venue['Address'] = $address[0];
            $venue['City'] = $address[1];
            $venue['Zip'] = $address[2];
        }
    
        // set up new_event data array according to tribe_create_event documentation
        // parse start and end dates and time as found in the Events Calendar's File_Importer_Events.php
        $new_event = [ 'EventStartDate'  => date( 'Y-m-d', $start_date ),
            'EventStartHour'        => date( 'h', $start_date ),
            'EventStartMinute'      => date( 'i', $start_date ),
            'EventStartMeridian'    => date( 'a', $start_date ),
            'EventEndDate'          => date( 'Y-m-d',$end_date ),
            'EventEndHour'          => date( 'h', $end_date ),
            'EventEndMinute'        => date( 'i', $end_date ),
            'EventEndMeridian'      => date( 'a', $end_date ),
            'post_title'      => $event_data['title'],
            'post_content'      => $event_data['description'],
            'post_status'      => 'publish',
            'EventURL'      => $event_data['link'],
            'guid'      => $event_data['guid'],
            'Venue'     => $venue
            ];
    
        // create event using tribe_create_event advanced function, return true if successful
        if ( tribe_create_event( $new_event ) != false ) {
            return true;
        }
        else {
            return false;
        }
    }


    /**
     * Parses RSS from the URL specified in options and creates an event for each item.
     * Returns number of new events created if successful, false otherwise.
     */
    function rte_import_rss() {
        
        // store feed information from options in $feed_info
        $feed_info = get_option( 'rte_saved' );
        
        // if we have a URL, attempt to load feed, parse RSS, and create an event
        if( isset( $feed_info['url'] ) ) {
            
            $num_created = 0;
            $rss = simplexml_load_file( $feed_info['url'] );
            if ( $rss !== false ) {
                
                // iterate over items in channel
                foreach ( $rss->channel->item as $item ) {
                
                    // make concatenated description
                    $description = (string) $item->$feed_info['description'];
                    if ( isset( $feed_info['optlabel'] ) ) {
                        $description = $description . $feed_info['optlabel'];
                    }
                    if ( isset( $feed_info['optdescript'] ) ) {
                        $addition = (string) $item->$feed_info['optdescript'];
                        $description = $description . $addition;
                    }
                    
                    // create $event_data array
                    $event_data = [
                        'title' => (string) $item->$feed_info['title'],
                        'description' =>  $description,
                        'stdatetime' => (string) $item->$feed_info['stdatetime'],
                        'endatetime' => (string) $item->$feed_info['endatetime'],
                        'locname' => (string) $item->$feed_info['locname'],
                        'address' => (string) $item->$feed_info['address'],
                        'guid'  => (string) $item->$feed_info['guid'],
                        'link' => (string) $item->$feed_info['link']
                    ];

                    // check if any required fields are empty
                    $valid_event = true;
                    foreach ( array('title', 'stdatetime', 'endatetime') as $required ) {
                        if ( !isset($event_data[$required]) ) {
                            $valid_event = false;
                        }
                    }
                    
                    // if the event is thus valid, create the event
                    if ( $valid_event ) {
                        
                        // ensure GUID field is not empty, for some reason it must appear to be a URL for get_id_from_guid() to work
                        if ( !isset( $event_data['guid']) || $event_data['guid'] == '' ) {
                        
                            // use link if possible
                            if ( isset( $event_data['link']) and $event_data['link'] != '' ) {
                                $event_data['guid'] = $event_data['link'];
                            }
        
                            // if no link available to use as GUID, convert the title into a URL-like appearance
                            else {
                                // add http & remove special characters, pattern from http://stackoverflow.com/questions/14114411/remove-all-special-characters-from-a-string
                                $event_data['guid'] = 'http://www.' . preg_replace( '/[^A-Za-z0-9\-]/', '', $event_data['title'] );
                            }
                        }
                        
                        // create event and update number of events created
                        if ( rte_create_event( $event_data ) == true ) {
                            $num_created += 1;
                        }
                    }    
                }
                return $num_created;
            }
        }
        return false;   
     }

?>