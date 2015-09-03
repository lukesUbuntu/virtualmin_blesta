/**
 * Created by Luke Hardiman on 2/09/2015.
 *
 * Moved general functions that are been used by most views to this file will tidy up and create proper helper js file
 *
 */

/**
 * Send ajax request
 */



if (typeof $CsrfToken !== "string" || typeof $action_url !== "string")
    throw new Error("pagehelper.js could not load properly");


console.log("pagehelper.js loaded okay")

var ErrorHolder     = $(".container #client_services_manage").parent();

function sendRequest($postVars,successCallback,errorCallback){
    //pass the token
    $postVars['_csrf_token'] = $CsrfToken;  //add token

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

            if (typeof errorCallback == "function")
                errorCallback(response);
        },
        beforeSend: function() {
            $("#ajax_loader",$CurrentPage).append($(this).blestaLoadingDialog());
        },
        complete: function() {
            $(".loading_container",$CurrentPage, $("#ajax_loader",$CurrentPage)).remove();
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
function attachError($message){
    ErrorHolder.prepend(
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
function attachSuccess($message){
    ErrorHolder.prepend(
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
 * @param errors
 */
function processErrors(response){
    console.log("response.data ",response.data );
    //check what type of error
    if (typeof response.data == "undefined"){
        console.log("Error checking response",response);
        return;
    }

    //3 types of errors that could be returned
    //****Single errors***/
    if (typeof response.data.errors == "string"){
        attachError(response.data.errors);
        return;
    }
    if (typeof response.data == "string"){
        attachError( response.data);
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

            attachError(error_message);

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
