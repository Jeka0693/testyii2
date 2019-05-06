<?php

namespace app\models;

use yii\db\ActiveRecord;

class Shop extends ActiveRecord{

    public static function tableName(){
        return 'shops';
    }


}

?>