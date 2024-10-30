<?php
/**
 * @license GNU General Public License version 2 (GPLv2)
 */
//Class containing common functions and constants

//Joomla JEXEC not included for cross platform (Wordpress, Drupal etc.) compatibility.
//Direct access to this file will result in declaration of abstract class and will exit.

abstract class Common {
    protected $MOBSTAC_BEHAVIOUR_VERSION = "2b";

    private function get_tld($url) {
        preg_match('@^(?:http://)?([^/]+)@i', $url, $matches);
        $host = $matches[1];
        // get last two segments of host name
        preg_match('/[^.]+\.[^.]+$/', $host, $matches);
        if (count($matches) > 0) {
            return $matches[0];
        }
        else {
            preg_match('@^(?:http://)?([^/]+)@i', $url, $matches);
            return $matches[1];
        }
    }

    function mobstac_check_required_fields($mobile_url, $mobstac_api_key) {
        if ($mobile_url && $mobstac_api_key) {
            return true;
        } else {
            return false;
        }
    }

    function mobstac_ping() {
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);
        if ($this->mobstac_check_required_fields($mobile_url, $mobstac_api_key)) {
            $fh = @fopen($mobile_url.'/m/ping/'.'?v='.$this->MOBSTAC_BEHAVIOUR_VERSION.'&mak='.$mobstac_api_key, 'r');
            if (!$fh) {
                return;
            }
            @fclose($fh);
        }
    }
    
    function get_api_key($mobile_url, $mobstac_api_key) {
        if ($this->mobstac_check_required_fields($mobile_url, $mobstac_api_key)) {
        	$file = file_get_contents('http://mobstac.com/m/matchapikey/?mobsite_url='.$mobile_url.'&api_key='.$mobstac_api_key);
        	$json = json_decode($file);
        	return $json->{'status'};
        }
    }

    //Pings MobStac if plugin is disabled
    function mobstac_plugin_disabled() {
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);
        if ($this->mobstac_check_required_fields($mobile_url, $mobstac_api_key)) {
            /*
            delete_option($this->MOBILE_URL);
            delete_option($this->MOBSTAC_API_KEY);
            delete_option($this->MOBSTAC_SITE_CREATED);
            delete_option($this->MOBSTAC_API_VERSION);
            */
			$fh = @fopen($mobile_url.'/m/plugindisabled/'.'?mak='.$mobstac_api_key, 'r');
            if (!$fh) {
                return;
            }
            @fclose($fh);
        }
    }
    
    private function mobstac_mane_param($complete_request_uri) {
        $maneref = '';
        if (isset($_SERVER['HTTP_REFERER'])) {
            $http_referer = urlencode(utf8_encode($_SERVER['HTTP_REFERER']));
            $maneref = 'maneref='.$http_referer;
        } else {
            return '';
        }

        if (strpos($complete_request_uri, '?') > 0) {
            return '&'.$maneref;
        }
            return '?'.$maneref;
    }
    
    function mobstac_handle_googlebot_mobile() {
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);
        if (!$this->mobstac_check_required_fields($mobile_url, $mobstac_api_key)) {
            return;
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Googlebot-Mobile') !== FALSE) {
            $complete_request_uri = utf8_encode($_SERVER['REQUEST_URI']);
            $maneref = $this->mobstac_mane_param($complete_request_uri);    
            if ($mobile_url != "") {
                $this->mobstac_redirect($mobile_url.$complete_request_uri.$maneref);
            }
        }
    }

    function mobstac_get_redirection_script() {
        $mobile_url = $mobstac_api_key = '';
        $this->mobstac_get_params($mobile_url, $mobstac_api_key);
        if ($this->mobstac_check_required_fields($mobile_url, $mobstac_api_key)) {
            $script=<<<JS_SNIP1
            try {
                var MSTAC = new Object();
JS_SNIP1;
            $script .= 'MSTAC.murl = "'.$mobile_url.'";';
            $script .= 'MSTAC.d = ".'.$this->get_tld($mobile_url).'";';
            $script .= PHP_EOL;
            $script .= <<<JS_SNIP2
            
                MSTAC.isMobile = (function(a){if(/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))return true})(navigator.userAgent||navigator.vendor||window.opera);
                if ((document.cookie.search("mstac=desktop") != -1) || (document.cookie.search("mstac_override=1") != -1)) {}
                else if (window.location.search.search("mstac=0") != -1) { 
                    document.cookie = 'mstac_override=1; path=/; domain=' + MSTAC.d + ';'; 
                } else if (MSTAC.isMobile) { 
                    domain = MSTAC.d; mstac_cookie = 'mstac=mobile; path=/; max-age=31536000'; if (domain) { mstac_cookie +='; domain=' + domain; } document.cookie = mstac_cookie; maneref = ''; if (document.referrer) { maneref = '?maneref=' + encodeURIComponent(document.referrer); } window.location = MSTAC.murl + window.location.pathname + maneref; 
                }else { 
                   var ms = document.createElement('script'); ms.type = 'text/javascript'; 
                   ms.src = MSTAC.murl + "/m/fastredirect/";
                   var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ms, s); 
                }
            } 
            catch(err) {}
JS_SNIP2;
        }
        else {
            $script = '';
        }
        $script .= PHP_EOL;
        return $script;
    }

    function mobstac_redirect_if_mobile() {
        // Do not redirect if override cokkie is set or mstac = 0
        if ($_GET['mstac'] === '0' || $_COOKIE['mstac_override'] === '1' || $_COOKIE['mstac'] === 'desktop') {
            if (!isset($_COOKIE['mstac_override'])) {
                setcookie('mstac_override', '1', 0, '/');
            }
            return;
        }
        $user_agents = array("Android", "iPhone", "iPod", "BlackBerry", "Nokia");
        
        foreach ($user_agents as $agent) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], $agent) !== false) {
                $mobile_url = $mobstac_api_key = '';
                $this->mobstac_get_params($mobile_url, $mobstac_api_key);
                if (!$this->mobstac_check_required_fields($mobile_url, $mobstac_api_key)) {
                    return;
                }
                $complete_request_uri = utf8_encode($_SERVER['REQUEST_URI']);
                $maneref = $this->mobstac_mane_param($complete_request_uri);    
                header("Location: $mobile_url".$complete_request_uri.$maneref);
                exit();
            }
        }
    }

    abstract protected function mobstac_redirect($url);
    abstract protected function mobstac_get_params(&$mobile_url, &$mobstac_api_key);
    abstract protected function mobstac_is_admin();
    abstract protected function mobstac_admin_warnings();
    abstract protected function mobstac_insert_script();

    //API Functions
    abstract protected function mobstac_get_categories();
    abstract protected function mobstac_get_content($type='default');
    abstract protected function mobstac_get_api_version();
    abstract protected function mobstac_get_platform_version();
}
?>
