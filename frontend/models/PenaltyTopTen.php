<?php

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class PenaltyTopTen extends Model
{
    public $fromDate;
    public $toDate;
    public $scenario;
    public $circle;
    public $device;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['fromDate', 'toDate', 'scenario'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'fromDate' => 'From Date',
            'toDate'   => 'To Date',
            'scenario' => 'Scenario',
            'circle'   => '',
            'device'   => '',
        ];
    }
}
