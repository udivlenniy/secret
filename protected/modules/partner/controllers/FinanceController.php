<?php
/**
 * Created by JetBrains PhpStorm.
 * User: artem
 * Date: 07.05.13
 * Time: 18:30
 * To change this template use File | Settings | File Templates.
 */

class FinanceController extends BaseUserController {


    public function actionIndex(){

        $this->render('index');
    }
}