//Search button Click
jQuery("#search_mandrill").click(function() {

    var mandrill_error_type = jQuery('#error_type').val();
    var start_date = jQuery('#search_form').find('input[name="start_date"]').val();
    var end_date = jQuery('#search_form').find('input[name="end_date"]').val();
    var noner = jQuery('#search_form').find('input[name="wnc_aiowz_tkn"]').val();

    if(mandrill_error_type != '' && start_date != '' && end_date != '') {
        jQuery("#example tbody").html('');
        jQuery('#search_mandrill').hide();
        jQuery('#clean_results').hide();
        
        var data = {
            'action': 'search_action',
            'error_type': mandrill_error_type,    // Create data object
            'start_date': start_date,
            'end_date': end_date,
            'controller': 'WNC_Mandrill_Cleaner',
            'wnc_aiowz_tkn': noner
        };
        // We can also pass the url value separately from ajaxurl for front end AJAX implementations
        jQuery.post(ajax_object.ajax_url, data, function (response) {

            thisSession = JSON.parse(response);

            //Error result?
            if (thisSession.hasOwnProperty('errors')) {
                for (var obj in thisSession){
                    var errrors = document.getElementById("search_error");
                    errors.innerHTML += data[obj].id+ "is"+data[obj].class + "<br/>";
                }
             }

             if (thisSession.hasOwnProperty('data_table')) {
                 var oTable = jQuery('#example').dataTable();

                 // Immediately 'nuke' the current rows (perhaps waiting for an Ajax callback...)
                 oTable.fnClearTable();

                 for (var key in thisSession['data_table']){
                     jQuery('#example').dataTable().fnAddData( [
                     '<input type="checkbox" class="checkBoxClass" name="check[]" value="' +  thisSession['data_table'][key].email + '" checked>Remove',
                         thisSession['data_table'][key].ts,
                         thisSession['data_table'][key].subject,
                         thisSession['data_table'][key].state,
                         thisSession['data_table'][key].email,
                         thisSession['data_table'][key].sender ] );
                 }

                 for (var nonce in thisSession['nonce']){
                     jQuery('#search_update').find('input[name="wnc_aiowz_tkn_update"]').val(thisSession['nonce']);
                 }

                 jQuery("#data_table_header").text('DataTable for ' + mandrill_error_type + ' from ' + start_date + ' to ' + end_date);
                 jQuery('#example').dataTable();
             }

            jQuery('#data_table_result').show();
            jQuery('#search_mandrill').show();
        });
    }
});

//Test Mandrill API button click
jQuery("#test_mandrill").click(function() {
    jQuery('#test_result').html('');
    var api_key = jQuery('#form1').find('input[name="password"]').val();
    jQuery('#test_mandrill').hide();
    var data = {
        'action': 'my_action',
        'api_key': api_key     // Create data object
    };
    // We can also pass the url value separately from ajaxurl for front end AJAX implementations
    jQuery.post(ajax_object.ajax_url, data, function(response) {
        jQuery('#test_result').html('<strong>' + response + '</strong>');
        jQuery('#test_mandrill').show();
    });
});

//Removing Emails from Mail Poet button click
jQuery("#clean_mandrill").click(function(e){
    e.preventDefault(); //prevent default form submit

    var r = confirm("This will disable the SELECTED emails from the Mail Poet newsletter list. Are you sure you want to do this?");
    if (r == true) {

        var oNonce = jQuery('#search_update').find('input[name="wnc_aiowz_tkn_update"]').val();

        var oTable = jQuery('#example').dataTable();

        var dataString = 'action=update_action&wnc_aiowz_tkn_update='+ oNonce + '&' + jQuery('input', oTable.fnGetNodes()).serialize();

        //alert(dataString);

        jQuery.post( ajax_object.ajax_url, dataString, function(response) {
            jQuery('#data_table_result').hide();
            jQuery('#clean_results').show();
            jQuery('#clean_results').html('<h2>Results:</h2>' + response);
        });


    } else {
        x = "You pressed Cancel!";
    }

    //alert(x);
    return false;

});

/**
 * Parses json array / object and displays error(s) within the dom element specified
 * @param obj json object
 * @param domElement Class/ID name of element to display errors within
 */
function displayJson( obj, domItem ) {
  for( node in obj ) {
    displayStr += node;
  }
    domItem.innterHTML = displayStr;
}

//Check all checkboxes on update form
jQuery(document).ready(function () {
    jQuery("#ckbCheckAll").click(function () {
            jQuery("#search_update input:checkbox").attr('checked', true);
    });
});

/*
jQuery('#testing').click( function() {

    var oTable = jQuery('#example').dataTable();
    var i = 0;
    var data = oTable.fnGetData();

    var sData = jQuery('input', oTable.fnGetNodes()).serialize();

    alert(data);
    alert(sData);
    jQuery(oTable.fnGetNodes()).each(function(){

        i++;
    });
    alert(oTable.fnGetNodes().serialize());
    alert(i);
});*/