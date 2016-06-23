<?php

/**
 * Penalty controller
 * @author Prashant Swami <prashant.s@infinitylabs.in>
 */

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class PenaltyController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'add-daily-data', 'weekly-data'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex() {
//        echo date('l', strtotime(date("Y-m-d")));
//        die;
        $connection = Yii::$app->mongodb;
        $database = $connection->getDatabase('deepdive');
        $collection = $database->getCollection('weeek_master');
        $tables = $collection->find(['status' => 0], ['table_name']);
        foreach ($tables as $table) {
            $table_name = $table['table_name'];
        }
        $collection = $database->getCollection('weekdayPenalty');
        $cursor = $collection->find();
        $data = array();
        foreach ($cursor as $doc) {
            echo "<pre/>";
            print_r($doc);
        }
    }

    public function actionAddDailyData() {
        error_reporting(E_ALL);
        ini_set("display_errors", 1);
        $db = Yii::$app->db_rjil;
        $command = $db->createCommand('SELECT * FROM tbl_built_penalty_points limit 10');
        $penelty_points = $command->queryAll();
        $day = date('D');
        $connection = Yii::$app->mongodb;
        $database = $connection->getDatabase('deepdive');
        $collection = $database->getCollection('weeek_master');
        $date = date("Y_m_d");
        $table_name = '';
        if ($day == 'Mon') {
            $collection = $database->getCollection('weeek_master');
            $collection->update([], ['$set' => ['status' => 1]]);
            $table_name = "weekday_penalty_" . $date;
            $collection->insert(['table_name' => "weekday_penalty_" . $date, 'status' => 0, 'date' => date('Y:m_d')]);
        } else {
            $tables = $collection->find(['status' => 0], ['table_name']);
            foreach ($tables as $table) {
                $table_name = $table['table_name'];
            }
        }
        $collection = $database->getCollection($table_name);
        $data = array();

        $connection->open();
        if (!empty($penelty_points)) {
            foreach ($penelty_points as $penelty_point) {
                $data = [
                    'hostname' => $penelty_point['hostname'],
                    'loopback0' => $penelty_point['loopback0'],
                    'device_type' => $penelty_point['device_type'],
                    'ios_compliance_status' => $penelty_point['ios_compliance_status'],
                    'ios_current_version' => $penelty_point['ios_current_version'],
                    'ios_built_version' => $penelty_point['ios_built_version'],
                    'bgp_available' => $penelty_point['bgp_available'],
                    'isis_available' => $penelty_point['isis_available'],
                    'resilent_status' => $penelty_point['resilent_status'],
                    'created_date' => $penelty_point['created_date'],
                ];
                $collection->insert($data);
                $data = array();
            }
        }
    }

    public function actionWeeklyData() {
        $connection = Yii::$app->mongodb;
        $database = $connection->getDatabase('deepdive');
        $collection = $database->getCollection('weeek_master');
        $tables = $collection->find(['status' => 0], ['table_name']);
        foreach ($tables as $table) {
            $table_name = $table['table_name'];
        }
        
    }

}
