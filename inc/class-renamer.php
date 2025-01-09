<?php
namespace CreactiveWeb;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Renamer {

    public static function init() {
        add_filter('sanitize_file_name', [ __CLASS__, 'wpcSanitizeFrenchChars' ], 10);
    }

    public static function wpcSanitizeFrenchChars($filename) {
        $filename = mb_convert_encoding($filename, "UTF-8");

        $char_not_clean = [
            '/À/','/Á/','/Â/','/Ã/','/Ä/','/Å/','/Ç/','/È/','/É/','/Ê/','/Ë/','/Ì/','/Í/','/Î/','/Ï/',
            '/Ò/','/Ó/','/Ô/','/Õ/','/Ö/','/Ù/','/Ú/','/Û/','/Ü/','/Ý/','/à/','/á/','/â/','/ã/','/ä/',
            '/å/','/ç/','/è/','/é/','/ê/','/ë/','/ì/','/í/','/î/','/ï/','/ð/','/ò/','/ó/','/ô/','/õ/',
            '/ö/','/ù/','/ú/','/û/','/ü/','/ý/','/ÿ/', '/©/'
        ];

        $clean = [
            'a','a','a','a','a','a','c','e','e','e','e','i','i','i','i',
            'o','o','o','o','o','u','u','u','u','y','a','a','a','a','a',
            'a','c','e','e','e','e','i','i','i','i','o','o','o','o','o',
            'o','u','u','u','u','y','y','copy'
        ];

        $friendly_filename = preg_replace($char_not_clean, $clean, $filename);

        $friendly_filename = utf8_decode($friendly_filename);
        $friendly_filename = preg_replace('/\?/', '', $friendly_filename);
        $friendly_filename = strtolower($friendly_filename);

        return $friendly_filename;
    }
}
