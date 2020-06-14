function generateActionMenu(email){
	
    console.log("TCL: generateActionMenu -> $email_address", $mail_accounts)
    $actionMenu = $("#action_menu");
}

function updateCurrentEmailAddress(email_address, id) {

currentEmail = {
    email_address: email_address,
    id: id
}

}

$(document).ready(function() {

generateActionMenu('wassup@myexample.com');
if (typeof sendRequest != "function") throw new Error("pagehelper.js has not loaded");

/**
 * Change password
 */
$('#changePassword', $CurrentPage).on('show.bs.modal', function(e) {

    //$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'))
    var email_id = $(e.relatedTarget).parent().data("email_id");

    //not correct way should be binding data to the element but its just for basic display for now
    var email_address = $("#email_" + email_id + ' > .email_address', MailAccountsDiv).text();

    console.log("email_address", email_address);
    $("#email_address", this).text(email_address);

    $(this).find('.change_password').click(function(e) {
        //doesn't matter if code gets injected as all code gets passed via blesta users domain
        new_password = $('#changePassword #change_password', $CurrentPage).val();

        var $postVars = {
            'email_address': email_address,
            'new_password': new_password,
            'action': "mail_change_password"
        };
        errorLocation = $("#changePassword .modal-body");
        sendRequest($postVars, function(response) {
            if (response.success == false)
                return processErrors(response, errorLocation);

            console.log("database changepassword response", response)
            $('#changePassword #change_password', $CurrentPage).val('')

            attachSuccess(response.data);
            return $('#changePassword').modal('toggle');

        }, errorLocation);
        //$('#confirm-delete').hide.bs.modal
        return e.preventDefault();
    })
});

/**
 * Confirm Deletion
 */
$('#ConfirmEmailDelete', $CurrentPage).on('show.bs.modal', function(e) {

    //$(this).find('.danger').attr('href', $(e.relatedTarget).data('href'))
    var email_id = currentEmail.id
    //$(e.relatedTarget).parent().data("email_id");

    //not correct way should be binding data to the element but its just for basic display for now
    var email_address = currentEmail.email_address
    // $("#email_" + email_id + ' > .email_address', MailAccountsDiv).text();

    $("#email_address", this).text(email_address);

    $(this).find('.danger').click(function(e) {

        var $postVars = {
            'email_id': email_id,
            'email_address': email_address,
            'action': "mail_delete_user"
        };

        sendRequest($postVars, function(response) {
            console.log("delete email response", response)

            if (response.success == true){
                $(".email_address").each(function(a,b){
                    console.log("b",b)
                    if ($(b).text() == email_address){
                       $(b).parent().remove()
                    }
                })
                $(".close").click()
            }
 

        });
        //$('#confirm-delete').hide.bs.modal
        return e.preventDefault();
    })
});
// save_email_forward
$("#addEmailForward", $CurrentPage).click(function(e) {
    var add_email_forward = $("#add_email_forward").val();
    if (/^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/.test(
            add_email_forward) == false) {
        alert("Invalid email forwarding address")
        return;
    }

    var $postVars = {
        'email_id': currentEmail.id,
        'email_address': currentEmail.email_address,
        'forward_email': add_email_forward,
        'action': "mail_add_forward"
    };
    console.log("postVars", $postVars)
    sendRequest($postVars, function(response) {
        console.log("delete email response", response)
        $('#addMailForward', $CurrentPage).modal('hide');


    });
    //$('#confirm-delete').hide.bs.modal


});
$("#disable_forward", $CurrentPage).click(function(e) {
    var email_id = $(this).parent().data("email_id");
    var email_address = $("#email_" + email_id + ' > .email_address', MailAccountsDiv).text();
    var $postVars = {
        'email_id': email_id,
        'email_address': email_address,
        'action': "mail_disable_forward"
    };
    sendRequest($postVars, function(response) {
        console.log("disabling forwarder", response)
        

    });

})
/*
 * Create Email Account
 */
$("#create_account", $CurrentPage).click(function(e) {

    e.preventDefault();
    //get our filled out form data
    var formData = $("#createEmailAccount", $CurrentPage).serializeFormToObject();

    sendRequest(formData, function(response) {

        if (response && response.success == false) {
            processErrors(response)
        } else {
            addEmailToList(formData);
            attachSuccess(response.data);
            toggleAddMail();
        }


    });
});

//toggle add mail div to show form
$(".add_mail", $CurrentPage).click(toggleAddMail);

//enable_mail_forward display textbox
$('#enable_mail_forward', $CurrentPage).change(function() {
    if ($(this).is(":checked")) {
        $(this).val("enabled");
        $("#email_forward_to").removeClass("hide")
    } else {
        $(this).val("");
        $("#email_forward_to").addClass("hide");
    }
});


});

/**
* Adds an email to our email list table
*/
function addEmailToList(emailForm) {
var template_action_menu = $("#template_action_menu").html();
var email_address = emailForm.add_mail_username + '@' + $Domain
template_action_menu = template_action_menu.replace(/%email%/gm,email_address)
$('tr:last', MailAccountsDiv).after('<tr><td>' + email_address +
    '</td> edit options have not been loaded<td></td><td></td> <td> ' + template_action_menu + ' </td></tr>')

}
/*
* Toggles between adding a mail address form
*/
function toggleAddMail() {
$("#createEmailAccount", $CurrentPage).toggleClass("hide");
$("#mail_accounts", $CurrentPage).toggleClass("hide");

//modify form
if ($(".add_mail", $CurrentPage).hasClass("btn-warning")) {
    $(".add_mail", $CurrentPage).toggleClass("btn-warning", "btn-success");
    $("span", ".add_mail", $CurrentPage).text("Add Mail Account");

} else {
    $(".add_mail", $CurrentPage).toggleClass("btn-warning");
    $("span", ".add_mail", $CurrentPage).text("Cancel");
}
}