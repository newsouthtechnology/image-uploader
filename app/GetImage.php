<?php
include_once('config.php');

class Get_Image{

    protected $_imagesPath = UPLOADS_PATH;

    protected $_maxWidth = 200;

    protected $_currentFileName = '';
    protected $_currentFilePath = '';
    protected $_currentFileInfo = array();

    public function __construct()
    {
        $this->_currentFileName = str_replace('|', '/', $_REQUEST['file']);
        $this->_currentFilePath = $this->_imagesPath.$this->_currentFileName;
        $info = getimagesize($this->_currentFilePath);
        $this->_currentFileInfo = array(
            'width' => $info[0],
            'height' => $info[1],
            'image_type' => $info[2],
            'mime' => $info['mime']
        );

        switch($_REQUEST['type']){
            case 'thumbnail':
            case 'thumb':
                $this->getThumbnail();
                break;
            default:
                $this->getImage();
                break;
        }
    }

    public function getThumbnail()
    {
        $info = getimagesize($this->_currentFilePath);
        list($width) = $info;

        if($width < $this->_maxWidth){
            $this->getImage();
        } else {
            return $this->smart_resize_image($this->_currentFilePath, $this->_maxWidth, 0, true, 'browser', false);
        }
    }

    public function getImage($output = 'browser')
    {
        switch ( $this->_currentFileInfo['image_type'] ) {
            case IMAGETYPE_GIF:   $image = imagecreatefromgif($this->_currentFilePath);   break;
            case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($this->_currentFilePath);  break;
            case IMAGETYPE_PNG:   $image = imagecreatefrompng($this->_currentFilePath);   break;
            default: return false;
        }

        switch ( strtolower($output) ) {
            case 'browser':
                $mime = image_type_to_mime_type($this->_currentFileInfo['image_type']);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $this->_currentFilePath;
                break;
            case 'return':
                return $image;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination
        switch ( $this->_currentFileInfo['image_type'] ) {
            case IMAGETYPE_GIF:   imagegif($image, $output);    break;
            case IMAGETYPE_JPEG:  imagejpeg($image, $output);   break;
            case IMAGETYPE_PNG:   imagepng($image, $output);    break;
            default: return false;
        }
    }

    public function smart_resize_image($file,
                                       $width              = 0,
                                       $height             = 0,
                                       $proportional       = false,
                                       $output             = 'file',
                                       $delete_original    = true,
                                       $use_linux_commands = false ) {

        if ( $height <= 0 && $width <= 0 ) return false;

        # Setting defaults and meta
        $info                         = getimagesize($file);
        $image                        = '';
        $final_width                  = 0;
        $final_height                 = 0;
        list($width_old, $height_old) = $info;

        # Calculating proportionality
        if ($proportional) {
            if      ($width  == 0)  $factor = $height/$height_old;
            elseif  ($height == 0)  $factor = $width/$width_old;
            else                    $factor = min( $width / $width_old, $height / $height_old );

            $final_width  = round( $width_old * $factor );
            $final_height = round( $height_old * $factor );
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
        }

        # Loading image to memory according to type
        switch ( $info[2] ) {
            case IMAGETYPE_GIF:   $image = imagecreatefromgif($file);   break;
            case IMAGETYPE_JPEG:  $image = imagecreatefromjpeg($file);  break;
            case IMAGETYPE_PNG:   $image = imagecreatefrompng($file);   break;
            default: return false;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
            $transparency = imagecolortransparent($image);

            if ($transparency >= 0) {
                $transparent_color  = imagecolorsforindex($image, $trnprt_indx);
                $transparency       = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            }
            elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, false);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, true);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);

        # Taking care of original, if needed
        if ( $delete_original ) {
            if ( $use_linux_commands ) exec('rm '.$file);
            else @unlink($file);
        }

        # Preparing a method of providing result
        switch ( strtolower($output) ) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination
        switch ( $info[2] ) {
            case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
            case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output);   break;
            case IMAGETYPE_PNG:   imagepng($image_resized, $output);    break;
            default: return false;
        }

        return true;
    }
}
$getImage = new Get_Image();