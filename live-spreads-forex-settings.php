<?php
    if (!function_exists('is_admin')) {
        header('Status: 403 Forbidden');
        header('HTTP/1.1 403 Forbidden');
        exit();
    }

    if (!class_exists("Ccc_Live_Spreads_Forex_Settings")) :

        class Ccc_Live_Spreads_Forex_Settings {

            public static $default_settings = 
            array(     
                'live_spreads_forex_url' => 'http://trader.tools/wp07/',
            );

            var $pagehook, $page_id, $settings_field , $options;


            function __construct() {    
                $this->page_id = 'live_spreads_forex';
                $this->settings_field = 'live_spreads_forex_options';
                
                $this->settings = get_option( $this->settings_field );

                add_action('admin_init', array($this,'admin_init'), 20 );
                add_action( 'admin_menu', array($this, 'admin_menu'), 20);
            }

            function admin_init() {
                register_setting( $this->settings_field, $this->settings_field, array($this, 'sanitize_theme_settings') );
                add_option( $this->settings_field , Ccc_Live_Spreads_Forex_Settings::$default_settings );

                add_settings_section('live_spreads_forex_main', '',  
                array($this, 'main_section_text'), 'live_spreads_forex_page');

                add_settings_field('live_spreads_forex_text', 'Live Spreads Forex Settings', 
                array($this, 'live_spreads_forex_text'), 'live_spreads_forex_page', 'live_spreads_forex_main');
                
            }

            function admin_menu() {
                if ( ! current_user_can('update_plugins') )
                    return;

                $this->pagehook = $page =  add_options_page(    
                __('Live Spreds Forex ', 'live_spreads_forex'), __('Live Spreads Forex', 'live_spreads_forex'), 
                'administrator', $this->page_id, array($this,'render') );

                add_action( 'load-' . $this->pagehook, array( $this, 'metaboxes' ) );

                add_action("admin_print_scripts-$page", array($this, 'js_includes'));
            }

            function js_includes() {
                wp_enqueue_script('jquery');
                wp_enqueue_script( 'postbox' );  
            }

            /*
            Sanitize our plugin settings array as needed.
            */    
            function sanitize_theme_settings($options) 
            {
                   $options['live_spreads_forex_url']      = isset($this->settings['live_spreads_forex_url']) ? (trim($options['live_spreads_forex_url'])?trim($options['live_spreads_forex_url']) :'http://trader.tools/wp07/') : 'http://trader.tools/wp07/' ;
                   return $options;
            }

            /*
            Settings access functions.
            */
            protected function get_field_name( $name , $option_type) {

                return sprintf( '%s[%s]', $this->$option_type, $name );

            }

            protected function get_field_id( $id ,$option_type) {

                return sprintf( '%s[%s]', $this->$option_type, $id );

            }

            protected function get_field_value( $key ,$option_type) 
            {
                return isset($this->settings[$key] ) ? $this->settings[$key]  : 'http://trader.tools/wp07/';
            }


            /*  Render settings page.*/
            function render() {
                global $wp_meta_boxes;

                $title = __('Live Spreads Forex', 'live_spreads_forex');
            ?>    
              

            <div class="wrap">   
                <h2><?php echo esc_html( $title ); ?></h2>
                <div class="metabox-holder">
                    <div class="postbox-container" style="width: 99%;">
                         <form name="live_spreads_form" id="live_spreads_form" method="post" action="options.php">
                            <div class="metabox-holder">
                                <div class="postbox-container" style="width: 99%;">
                                    <?php 
                                        settings_fields($this->settings_field); 
                                        
                                        do_meta_boxes( $this->pagehook, 'main', null );
                                        if ( isset( $wp_meta_boxes[$this->pagehook]['column2'] ) )
                                            do_meta_boxes( $this->pagehook, 'column2', null );
                                    ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script type="text/javascript">
                //<![CDATA[
                jQuery(document).ready( function ($) 
                {
                    $('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                    postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
                });
                
                function saveOptions()
                {
                    jQuery('#live_spreads_form').submit();  
                }
                
                //]]>        
            </script>
            
            <?php }

            function metaboxes() 
            {
                global $brsp_is_db_connected;
                add_meta_box( 'live-spreads-forex', __( 'Live Spreads Forex Settings', 'live_spreads_forex' ), array( $this, 'setting_box' ), $this->pagehook, 'main', 'high' );
            }

            
            function setting_box() 
            {   ?>

            <div class="symbol-content">
                <table>
                    <!--<tr>
                        <td><label for="<?php //echo $this->get_field_id( 'live_spreads_forex' , 'settings_field'); ?>" ><?php _e( 'Widget Status: ', 'broker_spreads_status' ); ?></label></td>
                        <td colspan="2">
                            <select id="broker_spreads_status" name="<?php //echo $this->get_field_id( 'broker_spreads_status' , 'settings_field'); ?>">
                                <option name="<?php //echo BROKER_SPREADS_STATUS_ENABLE_TEXT ; ?>" value="<?php //echo BROKER_SPREADS_STATUS_ENABLE; ?>"><?php //echo BROKER_SPREADS_STATUS_ENABLE_TEXT; ?> </option>
                                <option name="<?php //echo BROKER_SPREADS_STATUS_DISABLE_TEXT ; ?>" value="<?php //echo BROKER_SPREADS_STATUS_DISABLE; ?>"><?php //echo BROKER_SPREADS_STATUS_DISABLE_TEXT; ?> </option>
                            </select>
                        </td>
                    </tr>-->
                    <tr>
                        <td><label for="<?php echo $this->get_field_id( 'live_spreads_forex_url' , 'settings_field'); ?>" ><?php _e( 'Live Spreads Http Url: ', 'live_spreads_forex_url' ); ?></label></td>
                        <td>
                            <input type="textarea" name="<?php echo $this->get_field_name( 'live_spreads_forex_url' , 'settings_field'); ?>" class="live_spreads_logo_path_url" id="<?php echo $this->get_field_id( 'live_spreads_forex_url' , 'settings_field'); ?>" value="<?php echo esc_attr( $this->get_field_value( 'live_spreads_forex_url' ,'settings' ) ); ?>"/>
                        </td>
                        <td><?php _e( 'Ex :http://trader.tools/wp07/ ', 'live_spreads_forex_url' ); ?></td>
                    </tr>
                </table>
            </div> 
            
            <p>
                <input type="button" onclick="saveOptions();" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Settings'); ?>" />
            </p>
            <?php  }


    } 
    endif;
    
