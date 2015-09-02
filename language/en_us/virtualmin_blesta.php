<?php
/**
 * Language for VirtualMin Blesta Module
 */
//module
$lang['virtualmin.name'] = "Virtualmin ";

//module management
$lang['virtualmin.module_row'] = "Virtualmin Server";
$lang['virtualmin.module_row_plural'] = "Server";
$lang['virtualmin.add_module_row'] = "Add Server";
$lang['virtualmin.add_module_group'] = "Add Server Group";
$lang['virtualmin.manage.module_rows_title'] = "Servers";
$lang['virtualmin.manage.module_groups_title'] = "Server Groups";
$lang['virtualmin.manage.module_rows_heading.name'] = "Server Name";
$lang['virtualmin.manage.module_rows_heading.host_name'] = "Host Name";
$lang['virtualmin.manage.module_rows_heading.accounts'] = "Accounts";
$lang['virtualmin.manage.module_rows_heading.options'] = "Options";
$lang['virtualmin.manage.module_groups_heading.name'] = "Group Name";
$lang['virtualmin.manage.module_groups_heading.servers'] = "Server Count";
$lang['virtualmin.manage.module_groups_heading.options'] = "Options";
$lang['virtualmin.manage.servers.no_results'] = "There are no servers.";
$lang['virtualmin.manage.servers.groups.no_results'] = "There are no server groups.";
$lang['virtualmin.manage.module_rows.edit'] = "Edit";
$lang['virtualmin.manage.module_groups.edit'] = "Edit";
$lang['virtualmin.manage.module_rows.delete'] = "Delete";
$lang['virtualmin.manage.module_groups.delete'] = "Delete";
$lang['virtualmin.manage.module_rows.confirm_delete'] = "Are you sure you want to delete this server?";
$lang['virtualmin.manage.module_groups.confirm_delete'] = "Are you sure you want to delete this server group?";

//module management add row
$lang['virtualmin.row_meta.server_name'] = "Server Name";
$lang['virtualmin.row_meta.host_name'] = "Host Name";
$lang['virtualmin.row_meta.port_number'] = "Port : ";
$lang['virtualmin.row_meta.user_name'] = "User Name";
$lang['virtualmin.row_meta.password'] = "Password";
$lang['virtualmin.row_meta.use_ssl'] = "Use SSL when connecting to Virtualmin API (recommended).";
$lang['virtualmin.row_meta.use_ssl_warning'] = "Note: Module will fail if invalid SSL or any SSL cert issues.";
$lang['virtualmin.row_meta.account_limit'] = "Virtualmin Account Limit";
$lang['virtualmin.row_meta.name_server'] = "Name server %1\$s"; // %1$s is the name server number (e.g. 2)
$lang['virtualmin.manage.module_rows.count'] = "%1\$s / %2\$s"; // %1$s is the current number of accounts, %2$s is the total

// Add row
$lang['virtualmin.add_row.box_title'] = "Add Virtualmin Server";
$lang['virtualmin.add_row.basic_title'] = "Virtualmin Settings";
$lang['virtualmin.add_row.add_btn'] = "Add Server";
$lang['virtualmin.add_row.remove_name_server'] = "Remove";
$lang['virtualmin.add_row.name_server_btn'] = "Add Name Server";
$lang['virtualmin.add_row.test_connection_btn'] = "Test Connection";
$lang['virtualmin.add_row.name_servers_title'] = "Name Servers";
$lang['virtualmin.add_row.additional_settings'] = "Additional Settings";

// Edit row
$lang['virtualmin.edit_row.box_title'] = "Edit VirtualMin Server";
$lang['virtualmin.edit_row.basic_title'] = "Virtualmin Settings";
$lang['virtualmin.edit_row.name_servers_title'] = "Name Servers";
$lang['virtualmin.edit_row.name_server_btn'] = "Add Additional Name Server";
$lang['virtualmin.edit_row.name_server_col'] = "Name Server";
$lang['virtualmin.edit_row.name_server_host_col'] = "Hostname";
$lang['virtualmin.edit_row.remove_name_server'] = "Remove";
$lang['virtualmin.edit_row.save_btn'] = "Save Server";

//errors
$lang['virtualmin.!error.server_name.empty'] = "You must enter a Server Name.";
$lang['virtualmin.!error.host_name.format'] = "The Host Name appears to be invalid.";
$lang['virtualmin.!error.user_name.empty'] = "You must enter a User Name.";
$lang['virtualmin.!error.password.format'] = "You must enter a Password.";
$lang['virtualmin.!error.use_ssl.format'] = "Use SSL must be either true or false.";
$lang['virtualmin.!error.account_limit.valid'] = "Account Limit must be left blank (for unlimited accounts) or set to some integer value.";
$lang['virtualmin.!error.name_servers.valid'] = "One or more of the name servers entered are invalid.";
$lang['virtualmin.!error.name_servers.count'] = "You must define at least 2 name servers.";
$lang['virtualmin.!error.api.internal'] = "An internal error occurred, or the server did not respond to the request.";
$lang['virtualmin.!error.meta[package].empty'] = "A VirtualMin package is required.";
//$lang['virtualmin.!error.meta[ip].empty'] = "An IP address is required.";
$lang['virtualmin.!error.virtualmin_domain.format'] = "Please enter a valid domain name of eg: example.com";
$lang['virtualmin.!error.virtualmin_username.format'] = "The username may only contain alphanumeric characters.";
$lang['virtualmin.!error.virtualmin_username.length'] = "The username must be between 4 and 8 characters in length.";
$lang['virtualmin.!error.virtualmin_password.format'] = "The password may only contain ASCII characters.";
$lang['virtualmin.!error.virtualmin_password.length'] = "The password must be at least 5 characters in length.";
$lang['virtualmin.!error.virtualmin_email.format'] = "Please enter a valid email address.";
$lang['virtualmin.!error.virtualmin_password.matches'] = "Password and Confirm Password do not match.";
$lang['virtualmin.!error.mail_account.quota'] = "Quota must be number values only";

// Service fields
$lang['virtualmin.service_field.domain'] = "Domain";
//$lang['virtualmin.service_field.username'] = "Username";
//$lang['virtualmin.service_field.email'] = "Email";
$lang['virtualmin.service_field.password'] = "Password";
$lang['virtualmin.service_field.not_active'] = "Sorry your service is either not provisioned yet or is disabled";

//client tabs general

//client tabs - Client Tab Status (client_tab_status.pdt)
$lang['virtualmin.client.tabs.status.menu'] = "Server Status";
$lang['virtualmin.client.tabs.status.heading'] = "Hosting Overview";
$lang['virtualmin.client.tabs.status.disk_title'] = "Disk Usage";
$lang['virtualmin.client.tabs.status.disk_usage_unlimited'] = "(%1\$s MB/?)"; // %1$s is the amount of resource usage
$lang['virtualmin.client.tabs.status.disk_usage'] = "(%1\$s MB/%2\$s MB)"; // %1$s is the amount of resource usage, %2$s is the resource usage limit
$lang['virtualmin.client.tabs.status.bandwidth_title'] = "Bandwidth Usage";
$lang['virtualmin.client.tabs.status.server_details'] = "Server Details";
$lang['virtualmin.client.tabs.status.name_servers'] = "Name Server";
$lang['virtualmin.client.tabs.status.login_webmin'] = "Login to Control Panel";
$lang['virtualmin.client.tabs.status.web_folder'] = "Website Path";
$lang['virtualmin.client.tabs.status.web_address'] = "Website Address";

//client tabs - Client Tab Mail (client_tab_mail.pdt)
$lang['virtualmin.client.tabs.mail.menu'] = "Mail Accounts";

//client tabs - Client Tab Database (client_tab_database.pdt)
$lang['virtualmin.client.tabs.database.menu'] = "Databases";

?>