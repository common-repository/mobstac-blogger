<?php
/*
Plugin Name: MobStac WordPress Mobile
Plugin URI: http://mobstac.com/tour
Description: Redirects mobile visitors to a mobile version of your WordPress blog, powered by MobStac with blazing-fast page loads, support for over 5000 mobile devices (not just iPhones and touch phones), custom domain support (m.yourdomain.com), analytics, and ad network integration.
Version: 2.75
Author: MobStac
Author URI: http://mobstac.com
*/

$mobstac_plugin_version = "2.75";

if(!class_exists('Common')) {
    include_once('plugin_base.php');
}

class MobstacWordpress extends Common {
    protected $MOBSTAC_PLUGIN_VERSION = "mobstac_plugin_version";
    protected $MOBSTAC_API_KEY = "mobstac_api_key";
    protected $MOBILE_URL = "mobile_site_url";
    protected $MOBSTAC_SITE_CREATED = "mobstac_site_created";
    protected $MOBSTAC_ACTIVATION_TIME = "mobstac_activation_time";
    protected $MOBSTAC_DEACTIVATION_TIME = "mobstac_deactivation_time";
    protected $MOBSTAC_WP_VERSION = "mobstac_wp_version";
    
    function mobstac_redirect($url) {
		header('HTTP/1.1 301 Moved Permanently');
        header('Location: '.$url);
    }
	
	function on_activation() {
		global $mobstac_plugin_version;
		
		// in case this key is present, it means this is an update 2.75 onwards
		// update options
		if (get_option($this->MOBSTAC_PLUGIN_VERSION)) {
			update_option($this->MOBSTAC_PLUGIN_VERSION, $mobstac_plugin_version);
			
			$url = 'http://mobstac.com/mpa/wp/activation/';
			$fields = array(
						'plugin_version' => urlencode(get_option($this->MOBSTAC_PLUGIN_VERSION)),
						'wp_version' => urlencode(get_option($this->MOBSTAC_WP_VERSION)),
						'api_key' => urlencode(get_option($this->MOBSTAC_API_KEY)),
						'activation_time' => urlencode(get_option($this->MOBSTAC_ACTIVATION_TIME))
						);
			foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
			rtrim($fields_string,'&');
		
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$url);
			curl_setopt($ch,CURLOPT_POST,count($fields));
			curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
    		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_exec($ch);
			curl_close($ch);
		}
		// the key won't be present for most of the legacy users - activate / update and new installs.
		// add options
		else {
			add_option($this->MOBSTAC_PLUGIN_VERSION, $mobstac_plugin_version);
			add_option($this->MOBSTAC_SITE_CREATED, false);
			add_option($this->MOBSTAC_API_KEY);
			add_option($this->MOBILE_URL);
    		add_option($this->MOBSTAC_ACTIVATION_TIME, current_time('mysql'));
    		add_option($this->MOBSTAC_DEACTIVATION_TIME);
    		add_option($this->MOBSTAC_WP_VERSION, get_bloginfo('version'));
    		
			$msg = '<a href="plugins.php?page=mobstac-plugin-config">MobStac configuration </a>is incomplete! ';
			$msg .= 'Please<a href="plugins.php?page=mobstac-plugin-config"> enter your MobStac API key </a>';
			$msg .= 'for mobile / tablet redirection to work.';
			echo "<div class='updated'><p>" . __($msg) . "</p></div>";
		}
	}
	
	function on_deactivation() {
		$wp_version = get_bloginfo('version');
	    $curr_datetime = current_time('mysql');
		
		update_option($this->MOBSTAC_SITE_CREATED, false);
		update_option($this->MOBSTAC_DEACTIVATION_TIME, current_time('mysql'));
		
		$url = 'http://mobstac.com/mpa/wp/deactivation/';
		$fields = array(
					'plugin_version' => urlencode(get_option($this->MOBSTAC_PLUGIN_VERSION)),
					'api_key' => urlencode(get_option($this->MOBSTAC_API_KEY)),
					'deactivation_time' => urlencode(get_option($this->MOBSTAC_DEACTIVATION_TIME))
					);
		foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
		rtrim($fields_string,'&');
		
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_POST,count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_exec($ch);
		curl_close($ch);
    }
    
    function on_uninstallation() {
    	delete_option($this->MOBSTAC_PLUGIN_VERSION);
		delete_option($this->MOBSTAC_SITE_CREATED);
		delete_option($this->MOBSTAC_API_KEY);
		delete_option($this->MOBILE_URL);
    	delete_option($this->MOBSTAC_ACTIVATION_TIME);
    	delete_option($this->MOBSTAC_DEACTIVATION_TIME);
    	delete_option($this->MOBSTAC_WP_VERSION);
    }
    
	function mobstac_get_params(&$mobile_url, &$mobstac_api_key) {
        $mobile_url = $mobstac_api_key = '';
        $mobile_url = get_option($this->MOBILE_URL);
        $mobstac_api_key = get_option($this->MOBSTAC_API_KEY);
    }

	function mobstac_is_admin() {
	    return is_admin();
    }

	function mobstac_admin_warnings() {
        
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);
		
		if ('' == get_option('permalink_structure')) {
            echo "<div class='updated'><p>" . __('<strong>MobStac needs permalinks.</strong> Please go to the <a href="options-permalink.php">Permalinks Options Page</a> to configure your permalinks.') . "</p></div>";
        }
        
        if (!get_option($this->MOBSTAC_SITE_CREATED)){
        	echo "<div class='updated'><p>" . __('Please<a href="plugins.php?page=mobstac-plugin-config"> enter your MobStac API key </a>for mobile / tablet redirection to work.') . "</p></div>";
        }
    }

	function mobstac_redirect_if_mobile() {
	    if(!$this->mobstac_is_admin()) {
	        parent::mobstac_redirect_if_mobile();
	    }
	}
	
	function mobstac_insert_script() {
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);

        if ($this->mobstac_is_admin() || !$this->mobstac_check_required_fields($mobile_url, $mobstac_api_key)) {
            return;
        }

		if (get_option($this->MOBSTAC_SITE_CREATED)) {
			$script = '<script type="text/javascript">'.PHP_EOL;
            $script .= $this->mobstac_get_redirection_script();
            $script .= '</script>'.PHP_EOL;
            echo $script;
            $permapath = $_SERVER['REQUEST_URI'];
            echo '<link rel="alternate" type="text/html" media="handheld" href="'.$mobile_url.$permapath.'" />'.PHP_EOL;
		}
	}
	
    function mobstac_get_content($type='default') {
        if ($type === 'default' || $type === 'post') {
            $type = 'post';
        }
        else {
            $type = 'page';
        }
        $content_obj = wp_count_posts($type);
        $max_number = $content_obj->publish;
        if (isset($_GET['count'])) {
            // check if count is a numeric value greater than zero 
            if ((!is_numeric($_GET['count'])) || (int)$_GET['count'] < 1) {
                header("HTTP/1.1 400 Bad Request");
                header("Status: 400 Bad Request");
                echo '{"message" : "count should be a number greater than 0"}';
                exit();
            }
            $number = (int)$_GET['count'];
            if ($number > $max_number) {
                header("HTTP/1.1 400 Bad Request");
                header("Status: 400 Bad Request");
                echo '{"message" : "The max value of count can be '.$max_number.'"}';
                exit();
            }
        } else {
            $number = 5;
        }
        if (isset($_GET['offset']) && is_numeric($_GET['offset'])) {
            $offset = (int)$_GET['offset'];
            $total_number = $number + $offset;
            if (isset($_GET['count'])) {
                if($total_number > $max_number) {
                   header("HTTP/1.1 400 Bad Request");
                   header("Status: 400 Bad Request");
                   echo '{"message" : "offset value plus count can not be more than '.$max_number.'"}';
                   exit();
                }
            }
        } else {
            $offset = 0;
        }
        $content = get_posts(array('numberposts' => ($number + $offset), 'post_type' => $type));
        $result_array = array();
        $counter = 0;
        foreach($content as $post) {
            if ($counter >= $offset) {
                $post = (array)$post;
                if ($post['post_status'] == 'publish') {
                    $post['rssLink'] = get_permalink($post['ID']);
                    $post['title'] = $post['post_title'];
                    $post['content'] = $post['post_content'];
                    $post['updated'] = $post['post_modified_gmt'];
                    //post author is present as an id, according to api we need the name
                    $post['author'] = get_userdata($post['post_author'])->user_nicename;
                    $result_array[] = $post;
                }
            } else {
                $counter++;
            }
        }
        header("HTTP/1.1 200 OK");
        header("Status: 200 OK");
        echo FastJSON::encode($result_array);
        exit();
    }

    function mobstac_get_categories() {
        $categories = get_categories();
    	$result_array = array();
        foreach($categories as $category) {
            $category = (array)$category;
            $category['categoryName'] = $category['name'];
            $category['id'] = $category['cat_ID'];
            $arr = explode("?", $_SERVER['REQUEST_URI']);
            $category['categoryLink'] = "http://".$_SERVER['SERVER_NAME'].$arr[0].$category['slug']."/";
            $result_array[] = $category;
            }
        header("HTTP/1.1 200 OK");
        header("Status: 200 OK");  
        echo FastJSON::encode($result_array);
        exit();
    }

    function mobstac_get_api_version() {
        header("HTTP/1.1 200 OK");
        header("Status: 200 OK");    
        echo '{"version" : "'.$this->MOBSTAC_API_VERSION.'"}';
        exit();
    }

    function mobstac_get_platform_version() {
        header("HTTP/1.1 200 OK");
        header("Status: 200 OK");
        echo '{"platform" : "Wordpress" ,"version" : "'.get_bloginfo('version').'"}';
        exit();
    }

    function mobstac_api_handler() {
        // any new api methods should be added to this list
        $MOBSTAC_API_DEFINED_METHODS = array("getContent", "getCategories", "getApiVersion", "getPlatformVersion");
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);

        //check if the url is a mobstac api url
        if (!(strpos(strtolower($_SERVER['REQUEST_URI']), 'mobstac-api') > 0)) {
            return;
        }
        //check if the required plugin params are set
        if (!$this->mobstac_check_required_fields($mobile_url, $mobstac_api_key)) {
            header("HTTP/1.1 500 Server Error");
            header("Status: 500 Server Error");
            echo '{"message" : "MobStac configuration incomplete!"}';
            exit();
        }
        //authenticate the api key
        if ($_GET['apikey'] != get_option($this->MOBSTAC_API_KEY)) {
            header("HTTP/1.1 403 Forbidden");
            header("Status: 403 Forbidden");
            echo '{"message" : "API Key value does not match"}';
            exit();
        }
        //check if the request is for a valid method
        if (!in_array($_GET['method'], $MOBSTAC_API_DEFINED_METHODS)) {
            header("HTTP/1.1 501 Method Not Implemented");
            header("Status: 501 Method Not Implemented");
            echo '{"message" : "The requested method is yet to be implemented."}';
            exit();
        }
        //getMobstacVersion api method implementation
        if ($_GET['method'] == 'getApiVersion') {
            $this->mobstac_get_api_version();
        }
        if ($_GET['method'] == 'getPlatformVersion') {
            $this->mobstac_get_platform_version();
        }
        if (!class_exists('FastJSON')) {
            include_once('FastJSON.class.php');
        }
        if ($_GET['method'] == 'getContent') {
	    	if (!isset($_GET['type'])) {
	    	    $type = 'default';
	    	}
	    	else {
	    	    $type = $_GET['type'];
    	    }
	    	$this->mobstac_get_content($type);
        }
        //getCategories api method implementation
        if ($_GET['method'] == 'getCategories') {
	        $this->mobstac_get_categories();
        }
    }

    function mobstac_wp_dashboard_setup() {
        wp_add_dashboard_widget( 'mstac_wp_dashboard', __( 'Your MobStac powered mobile site' ), array($this, 'mobstac_wp_dashboard') );
    }

    function mobstac_wp_dashboard() {
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);
        if (!($this->MOBSTAC_SITE_CREATED)) {
    ?>
        You have not entered your MobStac API key!<br/><br/>
        <a class="button-primary" href="plugins.php?page=mobstac-plugin-config">Enter the API key</a>
    <?php
        } else {
            echo "<iframe src='http://b.mobstac.com/mpa/wpdash/?mak=" . $mobstac_api_key. "' width='500' height='420'></iframe>";
        }
    }

    function mobstac_plugin_menu() {
        if (!isset($mobstac_obj)) {
            $mobstac_obj = new MobstacWordpress();
        }
        add_submenu_page('plugins.php', 'MobStac B! Plugin', 'MobStac Configuration', 'manage_options', 'mobstac-plugin-config', array($mobstac_obj, 'mobstac_plugin_options'));
    }
	
	function mobstac_plugin_options() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // variable name for the hidden field
        $hidden_field_name = 'mt_submit_hidden';
        
        // hidden field to edit form
        $edit_settings_form = 'mt_settings_hidden';
        
		// Read in existing option value from database
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);
        
        if ($_POST[$edit_settings_form] == 'Y') {
        	update_option($this->MOBSTAC_SITE_CREATED, false);
        }
        
        // See if the user has posted us some information
        // If they did, this hidden field will be set to 'Y'
        if (isset($_POST[$this->MOBILE_URL]) && isset($_POST[$this->MOBSTAC_API_KEY]) && $_POST[$hidden_field_name] == 'Y') {
            // Read their posted value
            $mobile_url = trim($_POST[$this->MOBILE_URL]);
            $mobstac_api_key = trim($_POST[$this->MOBSTAC_API_KEY]);
            $mobile_url = rtrim($mobile_url, '/');
            if (!stristr($mobile_url, "http://") && $mobile_url) {
                $mobile_url = 'http://'.$mobile_url;
            }
            
			// Validate the API Key here
			if (strlen($mobile_url) > 0 && strlen($mobstac_api_key) > 0) {
				$url = 'http://mobstac.com/m/matchapikey/?mobsite_url='.$mobile_url.'&api_key='.$mobstac_api_key;
				$ch = curl_init();
				curl_setopt($ch,CURLOPT_URL,$url);
    			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
				$json = json_decode(curl_exec($ch));
				curl_close($ch);
    			if ($json->{'status'} == true) {
    				// Save the posted value in the database
    				update_option($this->MOBSTAC_SITE_CREATED, true);
            		update_option($this->MOBILE_URL, $mobile_url);
            		update_option($this->MOBSTAC_API_KEY, $mobstac_api_key);
        		} else {
        			echo '<div class="updated"><p>API Keys do not match.</p></div>';
        		}
			} else {
				echo '<div class="updated"><p>Both API Key and Site URL are mandatory fields.</p></div>';
			}
        }
    ?>

    <div class="wrap">
      <?php
      if (get_option($this->MOBSTAC_SITE_CREATED)){
      ?>
      	<div class="updated">
        	<p>Congratulations! Your MobStac site is ready. Access your <a target="_blank" href="http://mobstac.com/mpa/"> MobStac dashboard here</a>.</p>
        </div>
        <form name="settings_info_form" method="post" action="">
        	<input type="hidden" name="<?php echo $edit_settings_form; ?>" value="Y">
          	<p><b>MobStac plug-in settings:</b></p>
          	<p><b>API Key  : </b><i><?php echo $mobstac_api_key; ?></i></p>
          	<p><b>Site URL : </b><i><?php echo $mobile_url; ?></i></p>
          	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Edit Settings') ?>" />
        </form>
        <br/>
      	<div>
      <?php
        echo "<iframe width='500' height='760' src='http://mobstac.com/mpa/plugin/iframe/?siteurl=". get_option('siteurl') ."'></iframe>";
      ?>
      	</div>
      <?php
      }
      else {
      ?>
      <h2>MobStac Plugin Activation</h2>
      <p>
	  You need to have a MobStac mobile site to which your mobile users will be redirected.<br>
	  <a href="http://mobstac.com/" target="_blank">Create a mobile site</a>, if you have not done so already.
	  </p>
      <hr />
      <form name="form1" method="post" action="">
        <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
        <p><b>Please enter the following details to get the plugin working.</b></p>
        <p><b>MobStac API Key</b><i>(<a href="http://mobstac.com/mpa/apikey/" target="_blank">Get your API Key</a>)</i>
        <p><input type="text" name="<?php echo $this->MOBSTAC_API_KEY; ?>" value="<?php echo $mobstac_api_key; ?>" size="40" /></p>
        </p>
		<p><b>MobStac Site URL</b><i> (Enter the URL of your mobile site e.g. http://mysite.mobstac.com)</i>
        <p><input type="text" name="<?php echo $this->MOBILE_URL; ?>" value="<?php echo $mobile_url; ?>" size="20" /></p>
        </p>
        <p class="submit">
        <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Activate Plugin') ?>" />
        </p>
      </form>
      <?php
      }
      ?>
    </div>
<?php
    }
}
$mobstac_obj = new MobstacWordpress();

//redirect the googlebot mobile as it can't execute a script
add_action('template_redirect', array($mobstac_obj, 'mobstac_handle_googlebot_mobile'), -1000);
// override function to enable admin logins through mobile devices
add_action('template_redirect', array($mobstac_obj, 'mobstac_redirect_if_mobile'), -999);
add_action('init', array($mobstac_obj, 'mobstac_api_handler'), -500);
add_action('wp_dashboard_setup', array($mobstac_obj, 'mobstac_wp_dashboard_setup'));
add_action('wp_head', array($mobstac_obj, 'mobstac_insert_script'), -1000);
add_action('admin_menu', array($mobstac_obj, 'mobstac_plugin_menu'));
add_action('publish_post', array($mobstac_obj, 'mobstac_ping'));
add_action('admin_notices', array($mobstac_obj, 'mobstac_admin_warnings'));

register_activation_hook(__FILE__, array($mobstac_obj, 'on_activation'));
register_deactivation_hook(__FILE__, array($mobstac_obj, 'on_deactivation'));
register_uninstall_hook(__FILE__, array($mobstac_obj, 'on_uninstallation'));
?>
