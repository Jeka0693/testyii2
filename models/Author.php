<?php

namespace app\models;

use yii\db\ActiveRecord;

class Author extends ActiveRecord{

    public static function tableName(){
        return 'authors';
    }

    

}


?>