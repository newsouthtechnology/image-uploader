<?php

class Image_Grid {

    protected static $_startPath = '';
    protected static $_files = array();
    protected static $_ignoreFiles = array();

    /**
     * Init
     * @param $_path
     * @param null $_parent
     */
    private function __construct($_path, $_parent = null)
    {
        self::$_ignoreFiles = array ('.','..', 'app');
        self::$_startPath = rtrim($_path, '/');
        self::$_files = $this->_readDirectory($_path, $_parent);

        return $this;
    }

    public static function init($path, $parent = null)
    {
        static $instance  = null;
        if($instance === null){
            $instance = new Image_Grid($path, $parent);
        }
        return $instance;
    }

    public static function getJsonScript()
    {
        return '<script type="text/javascript">'."\n".'var ImageGridFiles = '.self::getJSON().';'."\n</script>\n";
    }
    /**
     * Returns all the data in JSON format
     * @return string
     */
    public static function getJSON()
    {
        return json_encode(self::$_files);
    }

    /**
     * Used to create an object out of the file data.
     * @param null|string $_path
     * @param null|string $_parent
     * @return stdClass
     */
    protected static function _createFileObject($_path = null, $_parent = null)
    {
        $path_parts = pathinfo($_path);
        $obj = new stdClass();
        $obj->file      = $path_parts['basename'];
        $obj->extension = isset($path_parts['extension']) ? $path_parts['extension'] : 'none';
        $obj->filesize  = self::_formatBytes(filesize($_path));

        $obj->url       = BASE_URL.($_parent != null ? $_parent.'/':'').$obj->file;
        $obj->thumb_url = APP_URL.'GetImage.php?file='.($_parent != null ? $_parent.'|':'').$obj->file.'&type=thumb';
        return $obj;
    }

    /**
     * Formats floats into bytes
     * @param float|int $bytes
     * @param int $precision
     * @return string
     */
    protected static function _formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Recurses through a directory of a given path
     * @param string $_path
     * @param string $_parent
     * @return array
     */
    protected static function _readDirectory($_path, $_parent = ''){
        $handle = opendir($_path);

        $_files = array();

        while($_file = readdir($handle)){
            if($_file !== '.' && $_file !== '..'){
                if($obj = self::_createFileObject($_path.$_file, $_parent)){
                    if(is_dir($_file)){
                        $obj->url = BASE_URL.'?album='.$obj->file;
                        $obj->files = self::_readDirectory($_path.$_file.'/', $_file);
                    }
                    if(in_array($obj->extension, array('jpg', 'png', 'gif', 'jpeg')) || isset($obj->files)){
                        $_files[] = $obj;
                    }
                }

            }
        }
        return $_files;
    }
}