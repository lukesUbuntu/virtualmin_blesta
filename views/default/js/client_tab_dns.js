
$(document).ready(function () {
    renderDnsRecords();
})

function renderDnsRecords(){
    var dns_records_table = $("#dns_records_table > tbody:last-child");
    var row_template = $('.dns_row').html()
    for(var dns_index in dnsRecords){
        var record = dnsRecords[dns_index];
        var row = row_template;
        if (record.type.toLowerCase() == "soa"){
            continue;
        }
        row = $('<tr>' + row + '</tr>');
        $('.type', row).text(record.type)
        var value = record.value.toString();
        if (value.length > 50){
            value = value.substr(0,80) + '...'
        }
        $('.value', row).text(value)
        

        $(dns_records_table).append(row)
        // > tbody:last-child
    }
}