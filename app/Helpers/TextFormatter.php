<?php

namespace App\Helpers;

class TextFormatter
{
    public static function convertHtmlToWhatsappText($html)
    {
        $html = preg_replace('/<\s*(b|strong)[^>]*>(.*?)<\s*\/\s*(b|strong)>/i', '*$2*', $html);
        $html = preg_replace('/<\s*(i|em)[^>]*>(.*?)<\s*\/\s*(i|em)>/i', '_$2_', $html);
        $html = preg_replace('/<\s*br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\s*\/p\s*>/i', "\n", $html);
        $html = preg_replace('/<\s*p[^>]*>/i', "\n", $html);
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace("/[ \t]+/", ' ', $text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        return trim($text);
    }
}
