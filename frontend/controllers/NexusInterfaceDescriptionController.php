<?php

namespace frontend\controllers;

use Yii;
use frontend\models\NexusInterfaceDescription;
use frontend\models\NexusCdpNeighborsDetail;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * NexusInterfaceDescriptionController implements the CRUD actions for NexusInterfaceDescription model.
 */
class NexusInterfaceDescriptionController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all NexusInterfaceDescription models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => NexusInterfaceDescription::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NexusInterfaceDescription model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new NexusInterfaceDescription model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new NexusInterfaceDescription();
        $CDPmodel = new NexusCdpNeighborsDetail();
        $items= ArrayHelper::map(NexusInterfaceDescription::find()->all(),"site","site");
        $post = Yii::$app->request->post();
        $nodes='';
        $links='';

        if (!empty($post['NexusInterfaceDescription']) && $post['NexusInterfaceDescription']['site']!='') {
           $site = $post['NexusInterfaceDescription']['site'];
           $nodes=array();
            $links=array();
            $db = Yii::$app->db_rjil;
//        $sql = "SELECT * FROM dd_ipsla_errors WHERE substring(host_name,9,3) IN ('ESR','PAR') AND date(created_at)=date(now())";
        $sql = "SELECT * FROM tbl_nexus_interface_description WHERE site='$site'";
        $command = $db->createCommand($sql);
        $nexusInterface = $command->queryAll();
      
//            $nexusInterface = NexusInterfaceDescription::find()->where('site=:site and ssh_status=:ssh_status',['site'=>$site, 'ssh_status'=>'1'])->all();
//            $nexusInterface = NexusInterfaceDescription::find()->where('site=:site',['site'=>$site])->all();
            $srchostarr = array();
            foreach($nexusInterface as $topdata){
                $srchostarr[] = substr($topdata['hostname'],0,14);
            }
//            print_r($srchostarr);
            foreach($nexusInterface as $topdata){
                $flag=FALSE;
                $cdpsql = "select distinct remote_interface from tbl_nexus_cdp_neighbors_detail where mgmt_ip='".$topdata['mgmt_ip']."'";
                $cdpcommand = $db->createCommand($cdpsql);
                $nexusRemotes = $cdpcommand->queryAll();
//                $nexusRemotes = NexusCdpNeighborsDetail::find()->select(['remote_interface'])->distinct()->where('mgmt_ip=:mgmt_ip',['mgmt_ip'=>$topdata['mgmt_ip']])->all();   
                foreach ($nexusRemotes as $remote){
                    if(in_array(substr($remote['remote_interface'],0,14), $srchostarr)){
                       $links[] = array(
                        'hostname' => substr($topdata['hostname'],0,14), 
                        'loopback0' => substr($topdata['hostname'],0,14), 
                        'iconType' => 'Nexus',
                        'ring_status' => "1",
                        'media_type' => "",
                        'physical_interface_desc' =>$topdata['physical_interface_desc'],
                        'port_channel_desc' => $topdata['port_channel_desc'],
                        'source' =>substr($topdata['hostname'],0,14), 
                           'target' =>substr($remote['remote_interface'],0,14),
                         );
                     
                    $nodes[substr($topdata['hostname'],0,14)] = array('id' =>substr($topdata['hostname'],0,14), 
                        'hostname' => substr($topdata['hostname'],0,14), 
                        'loopback0' => $topdata['mgmt_ip'],
                        'iconType' => 'Nexus',
                        'ring_status' => "1",
                        'media_type' => "",
                        'physical_interface_desc' =>$topdata['physical_interface_desc'],
                        'port_channel_desc' => $topdata['port_channel_desc'],
//                        'source' => substr($topdata['hostname'],0,14),
//                        'target' =>substr($remote['remote_interface'],0,14),
                        );
                    $nodes[substr($remote['remote_interface'],0,14)] = array('id' =>substr($remote['remote_interface'],0,14), 
                        'hostname' => substr($remote['remote_interface'],0,14), 
                        'loopback0' => $topdata['mgmt_ip'],
                        'iconType' => 'Nexus',
                        'ring_status' => "1",
                        'media_type' => "",
                        'physical_interface_desc' =>$topdata['physical_interface_desc'],
                        'port_channel_desc' => $topdata['port_channel_desc'],
//                        'source' => substr($topdata['hostname'],0,14),
//                        'target' =>substr($remote['remote_interface'],0,14),
                        );
                    }
                }
                
                     
                     
            }
                
        } 
        $nodes = array_values($nodes);
            return $this->render('create', [
                'model' => $model,
                'items'=>$items,
                'nodes'=>$nodes,
             'links'=>$links
            ]);
       
    }

    /**
     * Updates an existing NexusInterfaceDescription model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing NexusInterfaceDescription model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the NexusInterfaceDescription model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NexusInterfaceDescription the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NexusInterfaceDescription::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
}
