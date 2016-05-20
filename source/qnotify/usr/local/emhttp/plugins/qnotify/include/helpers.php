<?
$action = array_key_exists('action', $_GET) ? $_GET['action'] : '';

if (!empty($action)) {
    if ($action == 'devices'){
        $api = json_decode(get_pushbullet_api($_GET['token'],'devices'),true);
        $return = (empty($api['devices'])) ? [] : $api['devices'];

        echo json_encode($return);
    }
    elseif($action == 'token'){
        $api = json_decode(get_pushbullet_api($token,'users/me'),true);
        if(array_key_exists('active', $api))
            $return = ['success' => true];
        else
            $return = ['error' => $api['error']['message']];
            
        echo json_encode($return);
    }
}


// get a json array of the contents of gihub repo
function get_pushbullet_api($token, $api) {
    $ch = curl_init();
    $ch_vers = curl_version();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Access-Token: $token", 'Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_USERAGENT, 'curl/'.$ch_vers['version']);
    curl_setopt($ch, CURLOPT_URL, "https://api.pushbullet.com/v2/$api");
    $content = curl_exec($ch);
    curl_close($ch);

    return $content;
}
?>