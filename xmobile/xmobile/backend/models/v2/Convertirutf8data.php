<?php
namespace backend\models\v2;
use yii\base\Model;
use Yii;

class Convertirutf8data extends Model
{
    public function __construct(){
       
    }

    public static function convert_to_utf8_recursively($dat)
    {
       if (is_string($dat)) {
          return utf8_encode($dat);
       } elseif (is_array($dat)) {
          $ret = [];
          foreach ($dat as $i => $d) $ret[ $i ] = self::convert_to_utf8_recursively($d);
 
          return $ret;
       } elseif (is_object($dat)) {
          foreach ($dat as $i => $d) $dat->$i = self::convert_to_utf8_recursively($d);
 
          return $dat;
       } else {
          return $dat;
       }
    }
    public static function remplaceString($string) {
        if(is_array($string) or is_object($string)){
            foreach ($string as $key => $value) {
               $string=str_replace("`", '', $string);
               $value=str_replace("'", '', $value);
               $value=str_replace('?', ' ', $value);
               $string[$key]=$value;
            }
        }
        elseif (!is_null($string)) {
            $string=str_replace("`", '', $string);
            $string=str_replace("'", '', $string);
            $string=str_replace('?', ' ', $string);
            return $string;
        }
        return $string;
    } 

}
?>

