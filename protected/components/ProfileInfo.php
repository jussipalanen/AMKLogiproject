<?php

Yii::import('zii.widgets.CPortlet');

class ProfileInfo extends CPortlet
{

    public function init() {
        $this->title = "Tiedot";
        parent::init();
    }
    
    protected function renderContent() {
        $this->render('profileInfo');
    }
}


?>
