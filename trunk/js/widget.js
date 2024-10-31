/**
 * Handle payments callbacks
 * 
 * @author OlyCash
 * @copyright OlyCash Inc.
 * 
 */


// Function to execute before the API request is made 
function olycashPreProcess()
{

}


// Run function below when syncronous payment methods are used
// Credit, debit, wallet etc ..
function olycashPostProcess(data)
{

    if(data['message'] == 'success' && data['payment_type'] != 'credit_code') {
        update_order(data, 'completed', 'Payment Completed By User');
    } 
    else update_order(data, 'on-hold', 'Manualy complete order if customer provides proof of payment. <br>');
} 


// Run function below when asyncronous payment methods are used
// Mobile money , card, crypto, etc..
function olycashPostResponse(data){
    if(data['message'] == 'success') {
        update_order(data, 'completed', 'Payment Completed By User');
    }
    else update_order(data, 'on-hold', 'Manualy complete order if customer provides proof of payment. <br>');
} 



// Update order details based on response from the callback
function update_order(data, status, note = ''){
    var data = {
        order_id:widget.order_id, 
        order_key:widget.order_key, 
        status:status,
        note:note
    }

    jQuery.post(widget.cburl, data, function(responseData){
        // Normal redirection
        var response = JSON.parse(responseData);
        if(response['status'] == 'completed') {
            // Customized action after payment
            if(widget.response_action && widget.response_type == 'url') location.href = widget.response_action;
            else if(widget.response_action && widget.response_type == 'js') executeCustomCode();
            else location.href = widget.url;
        }
        else window.location.href = widget.baseURL+'/checkout/order-pay/'+widget.order_id+'/?key='+widget.order_key;

	});
}