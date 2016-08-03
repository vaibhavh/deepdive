<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;
use console\models\PenaltyJobs;

class PenaltyJobsController extends Controller {
        public function actionDailyCollection() {
            echo "\n Cron service runnning - " . time() . "\n";
            PenaltyJobs::fetchDailyData();
        }
        
        public function actionWeeklyCollection() {
            echo "\n Cron service runnning - " . time() ."\n";
            PenaltyJobs::fetchWeeklyData();
        }
}