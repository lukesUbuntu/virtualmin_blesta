
$(".install").click(function(e){
    var script_name = $(this).data('script_name');
    var script = $scriptInstall[script_name];
    var $script_versions = $("#script_versions")
    
    var available_versions = script['available_versions'].split(" ")
   

    $(".script_name").text(script_name);
    $(".script_description").text(script['description']);
    console.log("TCL: script", script)
    console.log("TCL: available_versions", available_versions)
    for (var version_index in available_versions) {
        var version = available_versions[version_index];
        var option = new Option(version, version);
        $script_versions.append(option)

    }   
    // $(".script_name").text(script_name)
    // console.log("TCL: script_name", script_name)
})