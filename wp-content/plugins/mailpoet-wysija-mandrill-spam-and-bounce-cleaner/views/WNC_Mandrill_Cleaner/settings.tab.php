<?php
    if( !array_key_exists( 'smtp_data', $viewmodel )) {
        $viewmodel['smtp_data'] = array('host'=>'', 'port' => '', 'username' => '', 'password' => '');
    }
?>
<!-- Mandrill Settings Tab -->

<div id="tab1" class="tab active">
    <?php
    // header
    echo "<h3>" . __( 'Mandrill API Settings', 'menu-test' ) . "</h3>";

    //dynamically create new form
    $mandrill_form = new WNC_Form_Builder();

    $frmObj = $mandrill_form->startForm("#", 'post', 'form1' ) . PHP_EOL .

        $mandrill_form->addInput( 'hidden', 'controller', 'WNC_Mandrill_Cleaner' ) . PHP_EOL .

        $mandrill_form->addInput( 'hidden', 'action', 'save' ) . PHP_EOL .

        '<p> <label>SMTP Host:</label>' .
        $mandrill_form->addInput( 'text','host', $viewmodel['smtp_data']['host'], array('placeholder' => 'Host') ) . PHP_EOL .
        '</p><hr />' .

        '<label> <span>SMTP Port:</label>' .
        $mandrill_form->addInput( 'text', 'port' , $viewmodel['smtp_data']['port'], array('placeholder' => 'Port') ) . PHP_EOL .
        '</p><hr />' .

        '<p> <label>Username:</label>' .
        $mandrill_form->addInput( 'text', 'username', $viewmodel['smtp_data']['username'], array('placeholder' => 'Username') ) . PHP_EOL .
        '</p><hr />' .

        '<p> <label>Password / API Key:</label>' .
        $mandrill_form->addInput( 'password', 'password', $viewmodel['smtp_data']['password'], array('placeholder' => 'Password') ) . PHP_EOL .
        '</p><hr />' .

        //csrf nonce
        $mandrill_form->addInput( 'hidden', 'wnc_aiowz_tkn', wp_create_nonce( 'csrf-nonce' ) ) . PHP_EOL .
        '<p class="submit">' .
        '<span id="test_mandrill" class="button-primary"><strong>Test Credentials</strong></span> &nbsp;&nbsp;' .
        $mandrill_form->addinput( 'submit','Submit',esc_attr__( 'Save Settings' ), array('class' => 'button-primary') ) . PHP_EOL .
        '</p>' .

        $mandrill_form->endForm() ;

    echo $frmObj;
    ?>

    <div id="test_result"></div>

</div>