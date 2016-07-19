<?php

namespace frontend\models;

use Yii;
use Closure;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;

class CustomActionColumn extends ActionColumn {

    public $template = '{graph}';

    /**
     * Initializes the default button rendering callbacks.
     */
    protected function initDefaultButtons() {
        if (!isset($this->buttons['graph'])) {
            $this->buttons['graph'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => Yii::t('yii', 'Graph'),
                    'aria-label' => Yii::t('yii', 'Graph'),
                    'data-pjax' => '0',
                    'id' => 'modalButton',
                    'style' => 'cursor:pointer',
                    'class' => 'modalClick',
                        ], $this->buttonOptions);
                return str_replace('href', 'value', Html::a('<span class="glyphicon glyphicon-object-align-bottom"></span>', $url, $options));
            };
        }
    }

}

?>