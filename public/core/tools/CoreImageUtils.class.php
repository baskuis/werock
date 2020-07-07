<?php

/**
 * Core Image Utils
 *
 * PHP version 5
 *
 * @package Ukora
 * @author Bas Kuis <b@ukora.com>
 * @copyright 2012 Bas Kuis (http://www.ukora.com)
 * @license http://creativecommons.org/licenses/by-nc/3.0/ Attribution-NonCommercial 3.0 Unported (CC BY-NC 3.0)
 * @link http://www.ukora.com/cms/documentation/
 */
class CoreImageUtils {

    /**
     * Resize a base64 encoded image
     *
     * @param  $data - The image data, as a string
     * @param  $width - new image width
     * @param  $height - new image height
     * @param  $proportional - keep image proportional, default is no
     * @return boolean|resource
     */
    public static function imageResize($data = null, $width = 0, $height = 0, $proportional = true){

        /**
         * Assertions
         */
        if ($height <= 0 && $width <= 0) return false;
        if ($data === null) return false;

        /**
         * Setup - inspection
         */
        $info = getimagesizefromstring($data);
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;
        if ($proportional) {
            if ($width == 0){
                $factor = $height / $height_old;
            } elseif ($height == 0) {
                $factor = $width / $width_old;
            } else {
                $factor = min($width / $width_old, $height / $height_old);
            }
            $final_width = round($width_old * $factor);
            $final_height = round($height_old * $factor);
        } else {
            $final_width = ($width <= 0) ? $width_old : $width;
            $final_height = ($height <= 0) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromstring($data);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromstring($data);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefromstring($data);
                break;
            default:
                return false;
        }

        /**
         * Resize image
         */
        $imageResized = imagecreatetruecolor($final_width, $final_height);
        if (($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG)) {
            $transparency = imagecolortransparent($image);
            $palletSize = imagecolorstotal($image);
            if ($transparency >= 0 && $transparency < $palletSize) {
                $transparent_color = imagecolorsforindex($image, $transparency);
                $transparency = imagecolorallocate($imageResized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($imageResized, 0, 0, $transparency);
                imagecolortransparent($imageResized, $transparency);
            } elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($imageResized, false);
                $color = imagecolorallocatealpha($imageResized, 0, 0, 0, 127);
                imagefill($imageResized, 0, 0, $color);
                imagesavealpha($imageResized, true);
            }
        }
        imagecopyresampled($imageResized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);

        /**
         * Capture image data
         * TODO: Find more elegant way, replace usage of output buffering
         *
         */
        ob_start();
        switch ($info[2]) {
            case IMAGETYPE_GIF:
                imagegif($imageResized);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($imageResized);
                break;
            case IMAGETYPE_PNG:
                imagepng($imageResized);
                break;
            default:
                return false;
        }
        $image =  ob_get_contents();
        ob_end_clean();
        return base64_encode($image);

    }

}