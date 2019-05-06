<?php

namespace app\models;

use yii\db\ActiveRecord;

class Book extends ActiveRecord{

    public static function tableName(){
        return 'books';
    }

    
   /* public static function getBooks($ids){
        if($ids){
            
        }
    }*/

}

?>