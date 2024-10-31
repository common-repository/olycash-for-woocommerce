/**
 * Handles payments
 * 
 * @author OlyCash
 * @copyright OlyCash Inc.
 * 
 */

jQuery(function($){

    $(document).on('submit','#mainform',function(){
        var formContainer = $(this).parents('div').first();
        var adminMail  = admin['admin_email'];
        var pluginId   = admin['plugin_id'];
        var OlyAccountMail = formContainer.find('#woocommerce_'+pluginId+'_email_address').val();
        var OlyWidgetId = formContainer.find('#woocommerce_'+pluginId+'_plugin_id').val();

        if(adminMail == OlyAccountMail && OlyWidgetId == ''){
            if(confirm('Do you wish to use '+OlyAccountMail+' as your registered business email contact?')){
                return true;
            } else return false;
        }
    });





    // Add url validation class if url is selected
    $(document).on('change', '.select-response', function(e){
        var trigger = $(this);
        var targetField = trigger.parents('table').first().find('.post-response-field');

        if(trigger.val() == 'url') targetField.addClass('validate-url');
        else targetField.removeClass('validate-url');

        targetField.val('');
    });





    // User is entering a URL
    $(document).on('change', '.validate-url', function(e){
        if(!validateUrl($(this).val())){
            alert('The URL you have provided is invalid');
            $(this).val('');
        }
    });

});


//Validates the URL entered.
function validateUrl(url){
    var myVariable = url;
        if(/^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(myVariable)) {
            return true;
        } else {
            return false;
        }
}