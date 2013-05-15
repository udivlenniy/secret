<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 14.05.13
 * Time: 14:59
 * To change this template use File | Settings | File Templates.
 */

class PartnerProgrammBehavior extends CActiveRecordBehavior {

    public $partnerModel = 'Partner';

    /*public function afterSave($event){
        if ($file = CUploadedFile::getInstance($this->owner, $this->fileField))){
            $this->deleteFile();
            $file->saveAs($this->filePath . '/' . $file->name);
            $this->owner->{$this->fileField} = $file->name;
        }
    }*/

}