<?php

namespace Jegarn\Util;

abstract class TextUtil {
    public static function isEmptyString($s){
        return $s === null || $s === '';
    }
    public static function generateGUID(){
        return md5(uniqid(mt_rand(), true));
    }
}