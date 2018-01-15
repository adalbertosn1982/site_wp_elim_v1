<!-- AJAJ Mandrill Cleanup Tab -->

<body class="dt-example">

<script>
    jQuery(function() {
        jQuery( "#datepicker" ).datepicker({         onSelect: function(dateText) {
            checkDateErrors();
        }, dateFormat: "mm-dd-yy" });
    });
    jQuery(function() {
        jQuery( "#datepicker2" ).datepicker({ onSelect: function(dateText) {
            checkDateErrors();
        }, dateFormat: "mm-dd-yy" });
    });

    function checkDateErrors(){
        if( jQuery( "#datepicker" ).val() <= jQuery( "#datepicker2" ).val() ) {
            //Then we're good
            jQuery('.start_date label').css('color', 'black');
            jQuery('.start_date #datepicker').css('color', 'black');
            jQuery( ".tab-content #search_error").html('');
        } else if( jQuery( "#datepicker" ).val() >= jQuery( "#datepicker2" ).val() && jQuery( "#datepicker2").val() != ''){
            jQuery( ".tab-content #search_error").html('Error: Start Date must be less than End Date.');
            jQuery('.start_date label').css('color', 'red');
            jQuery('.start_date #datepicker').css('color', 'red');
        }
    }

</script>

<div id="tab2" class="tab">
  <?php
        echo "<h3>" . __( 'Search for Errors / Clean Errors', 'menu-test' ) . "</h3>";

        if( array_key_exists( 'errors', $viewmodel )) {
            ?>
            <div style="color:#ff0000;">
                <?php
                foreach( $viewmodel['errors'] as $error) {
                    echo $error;
                } ?>
            </div>
                <?php
        }
   ?>

      <div id="description">
            <strong>Description: </strong>
            This will look for errors in your Mandrill account within the date range you specify. After it finds errors, you can click the "Clean Errors" button which will set the emails associated with the error to "Unsubscribed" in MailPoet. This will help you maintain your Mandrill rating in Good standing.
      </div>



      <h3>Error Type:</h3>

    <form action="#" method="post" id="search_form" name="search_form">
         <label> <strong>Mandrill problem to look for:</strong> </label><select name="error_type" id="error_type">
                <option name="spam">Spam</option>
                <option name="bounced">Bounced</option>
                <option name="soft-bounced">Soft-Bounced</option>
                <option name="rejected">Rejected</option>
                <option name="unsub">Un-Subscribed</option>
          </select>


          <h3>Date Range:</h3>
          <div class="start_date" style="float:left">
                <label for="start_date" id="start_date"><b>Start Date</b><br/>
                      <input type="text" placeholder="Month/Day" id="datepicker" name="start_date" class="validate[required]" maxlength="40" tabindex="4" />
                </label>
          </div>

          <div class="end_date" style="float:left">
                <label for="end_date" ><b>End Date</b><br/>
                      <input type="text" placeholder="Month/Day" id="datepicker2" name="end_date" class="validate[required]" maxlength="40" tabindex="4" />
                </label>
          </div>

        <div style="clear:both;"></div>

        <div id="search_error" style="padding-top:15px;"></div>

        <input type="hidden" name="wnc_aiowz_tkn" value="<?php echo wp_create_nonce( 'csrf-nonce-search' );?>" >
        <input type="hidden" name="controller" value="WNC_Mandrill_Cleaner">
        <input type="hidden" name="action" value="search">
        <br>

        <span id="search_mandrill" class="button-primary"><strong>Search for Errors</strong></span>( This will not clean errors yet, will do a preliminary search )
        <!--<input id="search_mandrill" type="submit" value="<?php echo esc_attr__( 'Search for Errors' );?>" class="button-primary">  -->
    </form>


    <div class="container" id="data_table_result" style="display:none;">
        <section>
            <h1 id="data_table_header">DataTable Result</h1>

            <div class="info">
                <p>Searching, ordering, paging of found data.</p>
            </div>

            <form name="search_update" id="search_update">
                <input type="hidden" name="action" value="update_action">
                <table id="example" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Remove? <!--<span id="ckbCheckAll" style="text-decoration:underline;">Check All</span>--></th>
                        <th>Date Sent</th>
                        <th>Email Subject</th>
                        <th>Mandrill Status</th>
                        <th>Sent To</th>
                        <th>From Email</th>
                    </tr>
                    </thead>

                    <tfoot>
                    <tr>
                        <th>Remove?</th>
                        <th>Date Sent</th>
                        <th>Email Subject</th>
                        <th>Mandrill Status</th>
                        <th>Sent To</th>
                        <th>From Email</th>
                    </tr>
                    </tfoot>
                    <tbody>

                    </tbody>
                </table>

                <input type="hidden" name="wnc_aiowz_tkn_update" value=""">
                <span id="clean_mandrill" class="button-primary"><strong>Clean Errors</strong></span> <strong>*** Will Disable <u>Checked</u> Emails from Mail Poet</strong></input>


            </form>
        </section>

    </div>

    <div id="clean_results">
    </div>

  <?php
    if( array_key_exists( 'data_table', $viewmodel )) {
  ?>
        <?php
        foreach( $viewmodel['data_table'] as $row) {
            //protected $mandrill_keys = array( 'ts', 'subject', 'state', 'email', 'sender' );
            ?>
            <tr>
                <td><input type="checkbox" checked></td>
                <td> <?php echo $row['td']; ?> </td>
                <td> <?php echo $row['subject']; ?> </td>
                <td> <?php echo $row['state']; ?> </td>
                <td> <?php echo $row['email']; ?> </td>
                <td> <?php echo $row['sender']; ?> </td>
            </tr>
        <?php
        }
        ?>
    <?php
     } //end if data_table exists
    ?>

</div>

</body>