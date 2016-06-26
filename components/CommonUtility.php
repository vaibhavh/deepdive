<?php
namespace app\components;

use yii\base\Component;
use yii\base\Exception;
use Yii;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class CommonUtility extends Component {
    public static function mongoDbConnection($database, $collection)
    {
        $connection = new \MongoClient(Yii::$app->mongodb->dsn);
        $database = $connection->$database;
        $collection = $database->$collection;
        return $collection;
    }
}
    ?>