<?php

class VMB_HELPER {

    public function __contruct() {

    }

    public function slugify($text) {
        return strtolower(trim(preg_replace('~[^\\pL\d]+~u', '-', iconv('utf-8', 'us-ascii//TRANSLIT', $text)), '-'));
    }

    function unslugify($slug) {
        return ucwords(str_replace('-', ' ', $slug));
    }

}