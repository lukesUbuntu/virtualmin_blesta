/**
 * Created by Luke Hardiman on 2/09/2015.
 *
 * Moved general functions that are been used by most views to this file will tidy up and create proper helper js file
 *
 * this javascript file has been thrown together as im concentrating on smashing out the php code then will define javascript structure better
 *
 */

/**
 * Send ajax request
 */

//$CurrentPage will be the bpdy of our plugin

if (typeof $CsrfToken !== "string" || typeof $action_url !== "string" || typeof $CurrentPage == "undefined")
    throw new Error("pagehelper.js could not load properly 17");
/*
if ($ instanceof jQuery)
    console.log("pagehelper.js loaded okay")
else
 throw new Error("pagehelper.js could not load properly 22");
 */



function sendRequest($postVars,successCallback,loadingElement){
    //pass the token
    $postVars['_csrf_token'] = $CsrfToken;  //add token

    //allow a loading div to be defined and append blesta ajax waiting.....
    loadingDiv = (loadingElement instanceof jQuery)?    loadingElement  : $CurrentPage;

    //remove any errors
    $(".error_section").remove();
    $.ajax({
        method: 'POST',
        url: actionUrl($postVars.action),   //render url
        data : $postVars,
        success: successCallback,
        //console.log(response);
        error : function(httpObj, textStatus) {
            //401 (Unauthorized) need to refresh page session died
            if (httpObj.status == 401){
                attachError("Session timed out refreshing possibley logged out reloading page...");
                setTimeout(function(){
                    location.reload();
                },4000);
                return;
            }
            //if not dump the works
            console.log("ajaxError httpObj->",httpObj);
            console.log("ajaxError textStatus->",textStatus);
            console.log("ajaxError $postVars ->",$postVars);

        },
        beforeSend: function() {
            loadingDiv.append($(this).blestaLoadingDialog());
        },
        complete: function() {
            $(".loading_container",loadingDiv).remove();
        },
        dataType: 'json'
    })
}

/*
 * Create URL & Get Request based on action url
 */
function actionUrl(action){
    var url = ($action_url.length < 1) ? "/" : $action_url;
    return ((url.substr(-1) != '/') ? url + "/" +  action : url + action);
}
/**
 * Attaches error message to the
 */
function attachError($message,toElement){
    error_holder =  (typeof toElement != "undefined") ? toElement :  $(".container #client_services_manage").parent();
    error_holder.prepend(
        '<section class=\"error_section\">'+
        '<article class=\"error_box error alert alert-danger alert-dismissable\">'+
        '<a href="#" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</a>'+
            //	'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">'+
        '<p>'+$message+'</p>'+
        '</article>'+
        '</section>'
    );
}
//attach success
function attachSuccess($message,toElement){
    error_holder =  (typeof toElement != "undefined") ? toElement :  $(".container #client_services_manage").parent();
    error_holder.prepend(
        '<section class=\"success_section\">'+
        '<article class=\"alert alert-success alert-dismissable\">'+
        '<a href="#" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">×</a>'+
            //	'<button type="button" class="close" data-dismiss="alert" aria-hidden="true">'+
        '<p>'+$message+'</p>'+
        '</article>'+
        '</section>'
    );
}

/**
 * process errors received from ajax
 * Will render to toElement if set otherwise $ErrorHolder will be used
 * @param errors
 */
function processErrors(response,toElement){
    console.log("response.data ",response.data );
    //check what type of error
    if (typeof response.data == "undefined"){
        console.log("Error checking response",response);
        return;
    }

    //3 types of errors that could be returned
    //****Single errors***/
    if (typeof response.data.errors == "string"){
        attachError(response.data.errors,toElement);
        return;
    }
    if (typeof response.data == "string"){
        attachError( response.data,toElement);
        return;
    }
    //****Multiple errors***/
    var our_errors = response.data.errors;

    if (typeof our_errors == "object"){
        $.each(our_errors,function(key,value){
            var error_message = false;
            if (typeof value.format == "string")
                error_message = value.format;

            if (typeof value.empty == "string")
                error_message = value.empty;

            attachError(error_message,toElement);

        })
    }

}
/**
 * Takes all inputs from element and renders to object
 *
 * @returns {{}} Object of form
 */
$.fn.serializeFormToObject = function()
{
    //get inputs from element
    var $inputs = $(":input",this);

    // get an associative array of just the values.
    var values = {};
    $inputs.each(function() {
        if (this.name.length > 1)
            values[this.name] = $(this).val();
    });

    return values;
};
/**
 * Document Ready
 */
$(document).ready(function () {
    /**
     * Show the main database password
     * $CurrentPage
     */
    $(".show_password").click(function () {
        $(this).hide();
        $(".show_password").removeClass('hide');
    });
})