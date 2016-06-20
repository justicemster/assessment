<?php

class functions {

    static function fileOnlyScan($dir, $sortType) {
        if(is_dir($dir)) {
            $indir = array_filter(scandir($dir, $sortType), function($item) {
                return !is_dir($item);          
            });
            sort($indir);
            return $indir;
        } else {
            return false;
        }
    }
    
}