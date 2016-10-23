<?
//exec($cmd, $output, $return_var=null);

if($return_var){

    // revert config file if there's an error with commit
    if(($commit) && !empty($config_old))
        file_put_contents($config_file, $config_old);

    $return = ['error' => $output];

}else
    $return = ['success' => true];

echo json_encode($return);
?>