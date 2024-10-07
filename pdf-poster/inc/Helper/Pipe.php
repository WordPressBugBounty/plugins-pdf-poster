<?php
namespace PDFPro\Helper;

class Pipe{
    public static function isPipe(){
        // $pdfp = \get_option('pdfp', false);
        // $activated = false;
        // if(is_array($pdfp)){
        //     $activated = $pdfp['active'] == '1' ? true : false;
        // }
        // return $activated;

        global $pdfp_bs;
        pdfp_fs()->can_use_premium_code();
    }

    public static function getPipeKey(){
        $pdfp = \get_option('pdfp', false);
        $key = '';
        if(is_array($pdfp)){
            $key = isset($pdfp['key']) ? $pdfp['key'] : '';
        }
        return $key;
    }

    public static function wasPipe(){
        $pdfp = \get_option('pdfp', false);
        
        if(!$pdfp){
            return false;
        }

        return true;
    }

}