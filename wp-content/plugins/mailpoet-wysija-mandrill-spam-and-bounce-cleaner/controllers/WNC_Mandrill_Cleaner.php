<?php
/**
 * Created by PhpStorm.
 * User: production
 * Date: 1/24/15
 * Time: 12:02 AM
 */

class WNC_Mandrill_Cleaner extends WNC_BaseController {

    //Default view
    function index() {
        $model = new WNC_Settings_Model;
        $this->ReturnView($model->getSettings(), true);
    }

    function save() {
        //Protect against CSRF
        if (!isset($_POST['wnc_aiowz_tkn'])) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
        if (!wp_verify_nonce($_POST['wnc_aiowz_tkn'],'csrf-nonce')) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

        $model = new WNC_Settings_Model();
        $this->ReturnView($model->save_settings(),true);
    }

    function search_action() {
        //Protect against CSRF
        if (!isset($_POST['wnc_aiowz_tkn'])) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
        if (!wp_verify_nonce($_POST['wnc_aiowz_tkn'],'csrf-nonce-search')) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

        $model = new WNC_Search_Model();
        //$this->ReturnView($model->search(),true);
        $model->search();
    }

    function update_action() {
        if (!isset($_POST['wnc_aiowz_tkn_update'])) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
        if (!wp_verify_nonce($_POST['wnc_aiowz_tkn_update'],'csrf-nonce-search-update')) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");

        $model = new WNC_Search_Model();
        $model->search_update();
    }


    function unsubscribe() {

    }

}