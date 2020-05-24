
 console.log("client_tab_status")
$(document).ready(function () {
   
    if (typeof VirtualMinModuleServerDetails == 'undefined')
        throw Error("can't load VirtualMinModuleServerDetails")
   
    processDiskUsage()
})



function processDiskUsage() {
    var disk_progress = $("#disk_progress");
    
    var server_quota_used = VirtualMinModuleServerDetails['server_quota_used'];
    $(".server_quota_used").text(server_quota_used);

   
    var server_quota =  VirtualMinModuleServerDetails['server_quota'];
    $(".server_quota").text(server_quota);
    
    console.log("TCL: processDiskUsage -> server_quota", server_quota)
    console.log("TCL: processDiskUsage -> VirtualMinModuleServerDetails", VirtualMinModuleServerDetails)
} 