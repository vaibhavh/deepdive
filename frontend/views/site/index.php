<?php

/* @var $this yii\web\View */
use yii\helpers\Url;

$this->title = 'My Yii Application';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Deepdive</h1>

<!--        <p class="lead">You have successfully created your Yii-powered application.</p>

        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>-->
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Top Ten Sites</h2>

                <p>Top ten sites that have poor performance and have maximum 
                   Penalty Points. Search by Period,Device Type and Region</p>

                <p><a class="btn btn-default" href="<?php echo Url::to(['/penalty-top-ten']); ?>">Top Ten Sites &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Penalty Points</h2>

                <p>Week wise Penalty Points for each device.</p>

                <p><a class="btn btn-default" href="<?php echo Url::to(['/penalty-points/points']); ?>">Penalty Points &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Device Penalties</h2>

                <p>Week wise Penalty Status for each device.</p>

                <p><a class="btn btn-default" href="<?php echo Url::to(['/penalty-points']); ?>">Device Penalties &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
