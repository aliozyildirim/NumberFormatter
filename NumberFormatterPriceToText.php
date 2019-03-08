<?php
/**
 * Created by PhpStorm.
 * User: ali.ozyildirim
 * Date: 2019-03-09
 * Time: 09:23
 */


class NumberFormatterPriceToText
{
    /**
     * @param string $currencyCode
     * @param $price
     * @return mixed
     */
    public static function getPriceText($currencyCode, $price)
    {
        $priceFormat    = new NumberFormatter(self::currencyMap($currencyCode), NumberFormatter::SPELLOUT);
        $priceText      = $priceFormat->format($price);
        
        $prices         = self::controlPrice(self::replaceText($currencyCode, $priceText), $currencyCode);
        
        return self::priceText($currencyCode, $prices);
    }
    
    /**
     * @param $currencyCode
     * @return mixed
     */
    public static function currencyMap($currencyCode)
    {
        // Chose Languge
        $array = [
            'TRY' => 'tr',
            'USD' => 'en',
            'EUR' => 'euro',
        ];
        return $array[$currencyCode];
    }
    
    /**
     * @param $currencyCode
     * @param $priceText
     * @return mixed
     */
    public static function replaceText($currencyCode, $priceText)
    {
        // Replace to string in text.
        $array = [
            'TRY' => str_replace('virgül', 'Lira', $priceText),
            'USD' => str_replace('point', 'Dollars', $priceText),
            'EUR' => str_replace('point', 'Euro', $priceText),
        ];
        return $array[$currencyCode];
    }
    
    /**
     * @param $currencyCode
     * @param $price
     * @return mixed
     */
    public static function priceText($currencyCode, $price)
    {
        $array = [
            'TRY' => 'Yalnız ' . $price,
            'USD' => 'Only ' . $price,
            'EUR' => 'Only ' . $price,
        ];
        return $array[$currencyCode];
    }
    
    /**
     * @param $price
     * @param $currencyCode
     * @return string
     */
    public static function controlPrice($price, $currencyCode)
    {
        // Price icerisinde virgul, point olmayabilir eger yoksa ekliyoruz.
        
        switch ($currencyCode) {
            case 'TRY':
                $text = 'Lira';
                break;
            case 'USD':
                $text = 'Dollars';
                break;
            case 'EUR':
                $text = 'Euro';
                break;
            default:
                $text = $currencyCode;
        }
        
        if( strstr($price, $text) ){
            return self::pointAfterReplace($price, $text, $currencyCode);
        } else {
            return $price . ' ' . $text;
        }
    }
    
    /**
     * @param $price
     * @param $text
     * @param $currencyCode
     * @return string
     */
    public static function pointAfterReplace($price, $text, $currencyCode)
    {
        $tmp = explode($text, $price);
        
        $search = [
            'TRY' => ['bir', 'iki', 'üç', 'dört', 'beş', 'altı', 'yedi', 'sekiz', 'dokuz'],
            'USD' => ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'],
            'EUR' => ['one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'],
        ];
        $replace = [
            'TRY' => ['on', 'yirmi', 'otuz', 'kirk', 'elli', 'altmış', 'yetmis', 'seksen', 'doksan'],
            'USD' => ['ten', 'twenty', 'thirty', 'fourty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'],
            'EUR' => ['ten', 'twenty', 'thirty', 'fourty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'],
        ];
        
        $aa = [
            'TRY' => 'Krş.',
            'USD' => 'Cents.',
            'EUR' => 'Sent.'
        ];
        
        $tmpr = ltrim($tmp[1], ' ');
        $t = explode(' ', $tmpr);
        
        if( count($t) > 1 ){
            
            $ta = str_replace($search[$currencyCode], $replace[$currencyCode], $t[0]);
            
            return $tmp[0] . $text . ' ' . $ta . ' ' . $t[1] . ' ' . $aa[$currencyCode];
            
        } else {
            
            $ta = str_replace($search[$currencyCode], $replace[$currencyCode], $tmp[1]);
            
            return $tmp[0] . $text . $ta . ' ' . $aa[$currencyCode];
        }
    }
}