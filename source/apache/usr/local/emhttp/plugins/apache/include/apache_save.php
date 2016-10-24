<?
$editfile = $_POST['editfile'];

// remove carriage returns
$editdata = (array_key_exists('editdata', $_POST)) ? str_replace("\r", '', $_POST['editdata']) : '';

// get previous config file contents and save them
$editdata_old = (file_exists($editfile)) ? file_get_contents($editfile) : '';
file_put_contents($editfile.'.old', $editdata_old);

// save file contents
$return_var = file_put_contents($editfile, $editdata);

if($return_var)
    $return = ['success' => true, 'saved' => $editfile];
else
    $return = ['error' => $editfile];

echo json_encode($return);
?>