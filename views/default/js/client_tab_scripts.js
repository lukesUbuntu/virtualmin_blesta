
var script_name = ''
$(".install").click(function(e){
    script_name = $(this).data('script_name');
    var script = $scriptInstall[script_name];
    var $script_versions_select = $("#script_version")
    var $database_select = $("#database")
    var available_versions = script['available_versions'].split(" ")
   

    $(".script_name").text(script_name);
    $(".script_description").text(script['description']);
    console.log("TCL: script", script)
    console.log("TCL: available_versions", available_versions)
    for (var version_index in available_versions) {
        var version = available_versions[version_index];
        var option = new Option(version, version);
        $script_versions_select.append(option)

    }   
    var db_name = dbName(script_name);
    var option = new Option(db_name, db_name);
    $database_select.append(option)
    for (var database_index in available_databases) {
        var database = available_databases[database_index];
        var option = new Option(database, database);
        $database_select.append(option)

    }
    $("#install_path").val(cleanScriptName(script_name))
    $(".install_path").text(cleanScriptName(script_name))

    // $(".script_name").text(script_name)
    // console.log("TCL: script_name", script_name)
})


$("#install_path").keyup(function(){
    $(".install_path").text(cleanScriptName($("#install_path").val()))
});

$(".removeScript").click(function(){
    script_name = $(this).data('script_name');
    var formData = {
    
        action : 'remove_script',
        script_name :  script_name
    }
    console.log("TCL: formData", formData)
    sendRequest(formData, function(response) {
        console.log("TCL: response", response)
    
    
     });
})
$("#installScript").click(function(){
    var formData = {
        action : 'script_install',
        script_name :  script_name
    }
   
    $(".form-control").each(function(a,b){
        formData[$(b).attr('id')] = $(b).val();
    })
    console.log("TCL: formData", formData)



    sendRequest(formData, function(response) {
    console.log("TCL: response", response)


    });


})

//https://www.virtualmin.com/documentation/install-scripts
function dbName(script_name){
    return  available_databases[0] + "_" + cleanScriptName(script_name);
}
function cleanScriptName(script_name){
    return script_name.replace(/\s|-|\.|:/g, '_').replace('__','_').toLowerCase();
}