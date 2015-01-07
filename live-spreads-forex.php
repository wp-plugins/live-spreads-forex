<?php
    /**
    * Plugin Name: Live Spreads - Forex
    * Plugin URI: http://www.trader.tools
    * Description: Add [live_spread] in your page and go to preview page to see live spreads data in table view.Any other enquiry, please contact to plugin site.
    * Version: 0.3
    * Author URI: http://www.trader.tools
    */

    define( 'CCCLIVE_SPREADS_FOREX_DIR', plugin_dir_path( __FILE__ ) );
    define( 'CCCLIVE_SPREADS_FOREX_URL', plugin_dir_url( __FILE__ ) );      

    if (!class_exists("Ccc_Live_Spreads_Forex")) :

        class Ccc_Live_Spreads_Forex {

            function __construct() 
            {
                if (is_admin()) 
                {
                    if (!class_exists("Ccc_Live_Spreads_Forex_Settings"))
                        require(CCCLIVE_SPREADS_FOREX_DIR .'live-spreads-forex-settings.php');
                    $this->settings = new Ccc_Live_Spreads_Forex_Settings();    
                }
                add_action('init', array($this,'init') );
            }

            function init() 
            {
                $this->initShortCodes();
            }

            function initShortCodes() {
                add_shortcode('live_spread', 'get_live_spread_widgetcode' );
            }
        }

        function get_live_spread_widgetcode(){

            try
            {
                global $LSForex;
                $settings = get_option('live_spreads_forex_options');

                if(!isset($settings['live_spreads_forex_url']))
                {
                    throw new Exception('Please save Live Spreads Http Url From Plugin Settings Page');
                }

                $resp = curlRequest($settings['live_spreads_forex_url']."?sdf654sdf654s6df54sd6f4sd65f4sd65f4sd65f4sdf654sd65f4sdf654=aubf6792dmnf84n9kadhf9urklfo748hie832d");
		
                if(!$resp)
                {
                    throw new Exception('Invalid Response.');
                }
			
                $result = json_decode($resp,true);

                if(!isset($result["content"]))
                {
                    throw new Exception('Invalid Response.');
                } 

                return html_entity_decode($result["content"]);
            }
            catch(Exception $e)
            {
                return $e->getMessage();
            }
        }

        function curlRequest($url) 
        {
            $ch=curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_REFERER, site_url());
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        } 


        endif;

    global $LSForex;
    if (class_exists("Ccc_Live_Spreads_Forex") && !$LSForex) {
        $LSForex = new Ccc_Live_Spreads_Forex();   
    }
?>
