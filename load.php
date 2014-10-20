<?php

require_once('app/config.php');
require_once('app/ImageGrid.php');

$album = isset($_REQUEST['album']) && !empty($_REQUEST['album']) ? $_REQUEST['album'] : null;
$imageGrid = Image_Grid::init(($album != null ? UPLOADS_PATH.$album.'/' : UPLOADS_PATH), $album);

function json_file_data(){
    global $imageGrid;
    echo $imageGrid::getJsonScript();
}

function json_file_data_raw(){
    global $imageGrid;
    echo $imageGrid::getJSON();
}

function get_url($path ='', $echo = true){
    $url = BASE_URL;
    if($path != ''){
        $url .= ltrim($path, '/');
    }
    if($echo){
        echo $url;
    }
    return $url;

}
