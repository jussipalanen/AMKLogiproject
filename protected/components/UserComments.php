<?php

Yii::import('zii.widgets.CPortlet');

class UserComments extends CPortlet
{

    public function init() {
        $this->title = "Kommentit";
        parent::init();
    }
    
    protected function renderContent() {
        $this->render('userComments');
    }
}


?>
