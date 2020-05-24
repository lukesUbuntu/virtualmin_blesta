<?php

/**
 * Created by PhpStorm.
 * User: Luke Hardiman
 * Date: 30/08/2015
 * Time: 12:51 PM
 * copyright Luke Hardiman 2015
 */
class VirtualminBlesta extends module
{

    /**
     * @var string The version of this module
     */
    private static $version = "0.1.2";
    /**
     * @var string The authors of this module
     */
    private static $authors = array(
        array('name' => "Luke Hardiman", 'url' => "https://github.com/lukesUbuntu")
    );

    /**
     * Initializes the API and returns a Singleton instance of that object for api calls
     * see ::Api()
     * @return VirtualMIN API The
     */
    private $_api = false;
    /**
     * Initializes the Virtualmin helper class and returns a Singleton instance of our helper
     * see ::getVirtualMinHelper()
     * @return VirtualMIN Helper Class
     */
    private $_virtualmin_lib_helper = false;

    /**
     * Initializes the module
     */
    public function __construct()
    {
        // Load components required by this module
        Loader::loadComponents($this, array("Input"));

        // Load the language required by this module
        Language::loadLang("virtualmin_blesta", null, dirname(__FILE__) . DS . "language" . DS);

        //added error reporting internally
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    }

    /**
     * Returns the name of this module
     *
     * @return string The common name of this module
     */
    public function getName()
    {
        return Language::_("virtualmin.name", true);
    }

    /**
     * Returns the version of this module
     *
     * @return string The current version of this module
     */
    public function getVersion()
    {
        return self::$version;
    }

    /**
     * Returns the name and URL for the authors of this module
     *
     * @return array A numerically indexed array that contains an array with key/value pairs for 'name' and 'url', representing the name and URL of the authors of this module
     */
    public function getAuthors()
    {
        return self::$authors;
    }

    /**
     * Returns the value used to identify a particular service
     *
     * @param stdClass $service A stdClass object representing the service
     * @return string A value used to identify this service amongst other similar services
     */
    public function getServiceName($service)
    {
        foreach ($service->fields as $field) {
            if ($field->key == "virtualmin_domain")
                return $field->value;
        }
        return null;
    }

    /**
     * Returns a noun used to refer to a module row (e.g. "Server", "VPS", "Reseller Account", etc.)
     *
     * @return string The noun used to refer to a module row
     */
    public function moduleRowName()
    {
        return Language::_("virtualmin.module_row", true);
    }

    /**
     * Returns a noun used to refer to a module row in plural form (e.g. "Servers", "VPSs", "Reseller Accounts", etc.)
     *
     * @return string The noun used to refer to a module row in plural form
     */
    public function moduleRowNamePlural()
    {
        return Language::_("virtualmin.module_row_plural", true);
    }

    /**
     * Returns a noun used to refer to a module group (e.g. "Server Group", "Cloud", etc.)
     *
     * @return string The noun used to refer to a module group
     */
    public function moduleGroupName()
    {
        return null;
    }

    /**
     * Returns the key used to identify the primary field from the set of module row meta fields.
     * This value can be any of the module row meta fields.
     *
     * @return string The key used to identify the primary field from the set of module row meta fields
     */
    public function moduleRowMetaKey()
    {
        return "server_name";
    }

    /**
     * Performs any necessary bootstraping actions. Sets Input errors on
     * failure, preventing the module from being added.
     *
     * @return array A numerically indexed array of meta data containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function install()
    {

    }

    /**
     * Performs migration of data from $current_version (the current installed version)
     * to the given file set version. Sets Input errors on failure, preventing
     * the module from being upgraded.
     *
     * @param string $current_version The current installed version of this module
     */
    public function upgrade($current_version)
    {

    }

    /**
     * Performs any necessary cleanup actions. Sets Input errors on failure
     * after the module has been uninstalled.
     *
     * @param int $module_id The ID of the module being uninstalled
     * @param boolean $last_instance True if $module_id is the last instance across all companies for this module, false otherwise
     */
    public function uninstall($module_id, $last_instance)
    {

    }


    /**
     * Returns the value used to identify a particular package service which has
     * not yet been made into a service. This may be used to uniquely identify
     * an uncreated service of the same package (i.e. in an order form checkout)
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @return string The value used to identify this package service
     * @see Module::getServiceName()
     */
    public function getPackageServiceName($packages, array $vars = null)
    {
        if (isset($vars['virtualmin_domain']))
            return $vars['virtualmin_domain'];
        return null;
    }

    /**
     * Adds the service to the remote server. Sets Input errors on failure,
     * preventing the service from being added.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being added (if the current service is an addon service and parent service has already been provisioned)
     * @param string $status The status of the service being added. These include:
     *    - active
     *    - canceled
     *    - pending
     *    - suspended
     * @return array A numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function addService($package, array $vars = null, $parent_package = null, $parent_service = null, $status = "pending")
    {


        //going to get the fields we need but passing some pacakage fields
        $params = $this->getFieldsFromInput((array)$vars, $package);

        //validate
        $this->validateService($package, $vars);

        if ($this->Input->errors())
            return;

        // Only provision the service if module is enabled
        //if $vars['virtualmin_domain_already']
        if (isset($vars['use_module']) && $vars['use_module'] == "true" && !isset($vars['virtualmin_domain_already'])) {
            //get row
            $row = $this->getModuleRow();

            //hide passwords from log
            $masked_params = $params;
            $masked_params['password'] = "****";
            $this->log($row->meta->host_name . "|" . 'addService', serialize($masked_params), "input", true);
            unset($masked_params);

            //get current clients details we want username
            Loader::loadModels($this, array("Clients"));
            $client = $this->Clients->get($vars['client_id'], false);


            //we are going to process the account under the blesta email as username
            $api = $this->api($row);

            //$result = $api->create($params['domain'],$params['passwd']);
            $account = array(
                'domain' => $params['domain'],
                'email' => $client->email,
                'user' => $params['domain'],
                'pass' => $params['passwd'],
                'plan' => $params['package'],
                'features-from-plan' => ''
            );

            //lets add the domain
            $result = $api->add_domain($account);
            $this->parseResponse($result);

            $this->log(
                $row->meta->host_name . "|" . 'addService', serialize($result), "output", true
            );


        }

        // Return service fields
        return array(
            array(
                'key' => "virtualmin_domain",
                'value' => $params['domain'],
                'encrypted' => 0
            ),
            array(
                'key' => "virtualmin_username",
                'value' => $params['domain'],
                'encrypted' => 0
            ),
            array(
                'key' => "virtualmin_password",
                'value' => $params['passwd'],
                'encrypted' => 1
            )
        );

    }

    /**
     * Returns an array of service field to set for the service using the given input
     *
     * @param array $vars An array of key/value input pairs
     * @param stdClass $package A stdClass object representing the package for the service
     * @return array An array of key/value pairs representing service fields
     */
    private function getFieldsFromInput(array $vars, $package)
    {
        $fields = array(
            'domain' => isset($vars['virtualmin_domain']) ? $vars['virtualmin_domain'] : null,
            //'username' => isset($vars['virtualmin_domain']) ? $this->usernameFromDomain($vars['virtualmin_domain']): null,
            'passwd' => isset($vars['virtualmin_password']) ? $vars['virtualmin_password'] : null,
            'package' => isset($package->meta->package) ? $package->meta->package : null
        );

        return $fields;
    }

    /**
     * Attempts to validate service info. This is the top-level error checking method. Sets Input errors on failure.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array $vars An array of user supplied info to satisfy the request
     * @return boolean True if the service validates, false otherwise. Sets Input errors when false.
     */
    public function validateService($package, array $vars = null, $editService = false)
    {
        // Validate the service fields
        $rules = array(

            'virtualmin_domain' => array(
                'format' => array(
                    'rule' => array(array($this, "validateHostName")),
                    'message' => Language::_("virtualmin.!error.virtualmin_domain.format", true)
                )
            ),
            'virtualmin_password' => array(
                'format' => array(
                    'rule' => array("matches", "/^[(\x20-\x7F)]*$/"), // ASCII 32-127
                    'message' => Language::_("virtualmin.!error.virtualmin_password.format", true)
                ),
                'length' => array(
                    'rule' => array("minLength", 5),
                    'message' => Language::_("virtualmin.!error.virtualmin_password.length", true)
                )
            )/*,
            'virtualmin_edit_action' => array(
                'format' => array(
                    'rule' => array(array($this, "validatePostActions")),
                    'message' => "Something is wrong with virtualmin_edit_action ;("
                )
            )*/
            //if we are editing on form action make sure we are set
        );

        //edit_action
        //if we are editing a service we do not need to verify all rules so we will unset ones we need
        if ($editService) {
            //add our rule for editing

            //loop all arrays
            foreach ($rules as $rule => $value) {
                // || $vars[$rule] == ""
                if (!array_key_exists($rule, $vars))
                    unset($rules[$rule]);
            }

        } else {
            //we are adding a service remove the edit_action
            //unset($rules["virtualmin_edit_action"]);
        }

        $this->Input->setRules($rules);
        return $this->Input->validates($vars);
    }

    /**
     *
     * Returns a singleton of Virtualmin API
     *
     * see lib/virtualmin_api.php
     *
     * @param bool|false $module_row
     * @return bool|VirtualMinApi
     */
    private function api($module_row = false)
    {
        //load our api
        if ($this->_api == false) {

            if ($module_row == false)
                $module_row = $this->getModuleRow();

            if (!isset($module_row)) {
                die("failed to get module row");
            }

            Loader::load(dirname(__FILE__) . DS . "lib" . DS . "virtualmin_api.php");

            //$host, $username, $password, $port = "10000", $use_ssl = true
            $this->_api = new VirtualMinApi(
                $module_row->meta->host_name,            //hostname
                $module_row->meta->user_name,            //username
                $module_row->meta->password,            //password
                $module_row->meta->port_number,            //port number
                ($module_row->meta->use_ssl == "true")    //use secure
            );
        }

        return $this->_api;
    }

    /**
     * Parses the response from the API into a stdClass object
     *
     * @param array $response The response from the API
     * @param boolean $return_response Whether to return the response, regardless of error
     * @return stdClass A stdClass object representing the response, void if the response was an error
     */
    private function parseResponse($response, $module_row = null, $ignore_error = false)
    {

        if (!$module_row)
            $module_row = $this->getModuleRow();

        $success = true;


        // Set an internal error on no response or invalid response
        if (empty($response)) {
            $this->Input->setErrors(
                array('errors' => Language::_("virtualmin.!error.api.internal", true))
            );
            $success = false;
        }

        // Set an error if given
        if (isset($response->error) || $response->status != "success") {

            $error = (isset($response->error) ? $response->error : Language::_("virtualmin.!error.api.internal", true));
            $this->Input->setErrors(
                array('errors' => $error)
            );
            $success = false;
        }

        //remove the full long error before logging
        if (isset($response->full_error))
            unset($response->full_error);

        // Log the response
        $this->log($module_row->meta->host_name, serialize($response), "output", $success);

        // Return if any errors encountered
        if (!$success && !$ignore_error)
            return;

        return $response;
    }

    /**
     * Edits the service on the remote server. Sets Input errors on failure,
     * preventing the service from being edited.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $vars An array of user supplied info to satisfy the request
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being edited (if the current service is an addon service)
     * @return array A numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function editService($package, $service, array $vars = array(), $parent_package = null, $parent_service = null)
    {
        return null;
    }

    /**
     * Cancels the service on the remote server. Sets Input errors on failure,
     * preventing the service from being canceled.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being canceled (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function cancelService($package, $service, $parent_package = null, $parent_service = null)
    {
        return null;
    }

    /**
     * Suspends the service on the remote server. Sets Input errors on failure,
     * preventing the service from being suspended.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being suspended (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function suspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        return null;
    }

    /**
     * Unsuspends the service on the remote server. Sets Input errors on failure,
     * preventing the service from being unsuspended.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being unsuspended (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function unsuspendService($package, $service, $parent_package = null, $parent_service = null)
    {
        return null;
    }

    /**
     * Allows the module to perform an action when the service is ready to renew.
     * Sets Input errors on failure, preventing the service from renewing.
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being renewed (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function renewService($package, $service, $parent_package = null, $parent_service = null)
    {
        return null;
    }

    /**
     * Updates the package for the service on the remote server. Sets Input
     * errors on failure, preventing the service's package from being changed.
     *
     * @param stdClass $package_from A stdClass object representing the current package
     * @param stdClass $package_to A stdClass object representing the new package
     * @param stdClass $service A stdClass object representing the current service
     * @param stdClass $parent_package A stdClass object representing the parent service's selected package (if the current service is an addon service)
     * @param stdClass $parent_service A stdClass object representing the parent service of the service being changed (if the current service is an addon service)
     * @return mixed null to maintain the existing meta fields or a numerically indexed array of meta fields to be stored for this service containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function changeServicePackage($package_from, $package_to, $service, $parent_package = null, $parent_service = null)
    {
        return null;
    }

    /**
     * Validates input data when attempting to add a package, returns the meta
     * data to save when adding a package. Performs any action required to add
     * the package on the remote server. Sets Input errors on failure,
     * preventing the package from being added.
     *
     * @param array An array of key/value pairs used to add the package
     * @return array A numerically indexed array of meta fields to be stored for this package containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function addPackage(array $vars = null)
    {

        $meta = array();
        if (isset($vars['meta']) && is_array($vars['meta'])) {
            // Return all package meta fields
            foreach ($vars['meta'] as $key => $value) {
                $meta[] = array(
                    'key' => $key,
                    'value' => $value,
                    'encrypted' => 0
                );
            }
        }

        return $meta;
    }

    /**
     * Validates input data when attempting to edit a package, returns the meta
     * data to save when editing a package. Performs any action required to edit
     * the package on the remote server. Sets Input errors on failure,
     * preventing the package from being edited.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param array An array of key/value pairs used to edit the package
     * @return array A numerically indexed array of meta fields to be stored for this package containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function editPackage($package, array $vars = null)
    {

        $meta = array();
        if (isset($vars['meta']) && is_array($vars['meta'])) {
            // Return all package meta fields
            foreach ($vars['meta'] as $key => $value) {
                $meta[] = array(
                    'key' => $key,
                    'value' => $value,
                    'encrypted' => 0
                );
            }
        }

        return $meta;
    }

    /**
     * Deletes the package on the remote server. Sets Input errors on failure,
     * preventing the package from being deleted.
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @see Module::getModule()
     * @see Module::getModuleRow()
     */
    public function deletePackage($package)
    {

    }

    /**
     * Returns the rendered view of the manage module page
     *
     * @param mixed $module A stdClass object representing the module and its rows
     * @param array $vars An array of post data submitted to or on the manage module page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the manager module page
     */
    public function manageModule($module, array &$vars)
    {
        // Load the view into this object, so helpers can be automatically added to the view

        $this->view = new View("manage", "default");
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView("components" . DS . "modules" . DS . "virtualmin_blesta" . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, array("Form", "Html", "Widget"));

        $this->view->set("module", $module);

        return $this->view->fetch();
    }

    /**
     * Returns the rendered view of the add module row page
     *
     * @param array $vars An array of post data submitted to or on the add module row page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the add module row page
     */
    public function manageAddRow(array &$vars)
    {

        
        // Load the view into this object, so helpers can be automatically added to the view
        $this->view = new View('add_row', 'default');
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView('components' . DS . 'modules' . DS . 'virtualmin_blesta' . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, ['Form', 'Html', 'Widget']);

        // Set unspecified checkboxes
        if (!empty($vars)) {
            if (empty($vars['use_ssl'])) {
                $vars['use_ssl'] = 'false';
            }
        }

        $this->view->set('vars', (object) $vars);
        return $this->view->fetch();
    }

    /**
     * Returns the rendered view of the edit module row page
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     * @param array $vars An array of post data submitted to or on the edit module row page (used to repopulate fields after an error)
     * @return string HTML content containing information to display when viewing the edit module row page
     */
    public function manageEditRow($module_row, array &$vars)
    {
        $this->view = new View("edit_row", "default");
        $this->view->base_uri = $this->base_uri;
        $this->view->setDefaultView("components" . DS . "modules" . DS . "virtualmin_blesta" . DS);

        // Load the helpers required for this view
        Loader::loadHelpers($this, array("Form", "Html", "Widget"));

        if (empty($vars))
            $vars = $module_row->meta;
        else {
            // Set unspecified checkboxes
            if (empty($vars['use_ssl']))
                $vars['use_ssl'] = "false";
        }

        $this->view->set("vars", (object)$vars);
        return $this->view->fetch();
    }

    /**
     * Adds the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being added.
     *
     * @param array $vars An array of module info to add
     * @return array A numerically indexed array of meta fields for the module row containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function addModuleRow(array &$vars)
    {
        
        $vars['host_name'] = strtolower($vars['host_name']);

        //our meta fields
        $meta_fields = array(
            "server_name",
            "host_name",
            "port_number",
            "user_name",
            "password",
            "use_ssl",
            "account_limit",
            "account_count",
            "name_servers"
        );
        //encrypted fields
        $encrypted_fields = array("user_name", "password");
       

       
        // Set use_ssl as false if not checked
        if (empty($vars['use_ssl']))
            $vars['use_ssl'] = "false";

        // Set rules to validate against
        $this->Input->setRules($this->getRowRules($vars));
       
        // Validate module row
       
        if ($this->Input->validates($vars)) {
            // Build the meta data for this row
            $meta = array();
           
            foreach ($vars as $key => $value) {

                if (in_array($key, $meta_fields)) {
                    $meta[] = array(
                        'key' => $key,
                        'value' => $value,
                        'encrypted' => in_array($key, $encrypted_fields) ? 1 : 0
                    );
                }
            }
            
            return $meta;
        }
    }


    /**
     * Retrieves a list of rules for validating adding/editing a module row
     *
     * @param array $vars A list of input vars
     * @return array A list of rules
     */
    private function getRowRules($vars)
    {
        $rules = [
            'server_name' => [
                'valid' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Virtualmin.!error.server_name_valid', true)
                ]
            ],
            'host_name' => [
                'valid' => [
                    'rule' => [[$this, 'validateHostName']],
                    'message' => Language::_('Virtualmin.!error.host_name_valid', true)
                ]
            ],
            'user_name' => [
                'valid' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Virtualmin.!error.user_name_valid', true)
                ]
            ],
            'port_number' => [
                'valid' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Virtualmin.!error.port_valid', true)
                ]
            ],
            'password' => [
                'valid' => [
                    'rule' => 'isEmpty',
                    'negate' => true,
                    'message' => Language::_('Virtualmin.!error.password_valid', true)
                ]
            ]
        ];

        return $rules;
    }

    /**
     * Validates connection by calling list_plans smallest transaction possible
     *
     */
    public function validateConnection($host, $account)
    {
        // print_r($password . $hostname . $port . $user . $realm);

        Loader::load(dirname(__FILE__) . DS . "lib" . DS . "virtualmin_api.php");
        try {
            $test = new VirtualMinApi(
                $host,            //hostname
                $account['user_name'],            //username
                $account['password'],            //password
                $account['port_number'],            //port number
                ($account['use_ssl'] == "true")    //use secure
            );


            $response = $test->list_plans();
            if ($response && $response->status == 'success') return true;

            $this->log($account['host_name'], serialize($response), "connection error", $response);

        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            /*
            $this->Input->setErrors(
                array('errors' => $errorMessage)
            );*/
            $this->log($account['host_name'], serialize($errorMessage), "connection error", $errorMessage);
        }


    }

    /**
     * Edits the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being updated.
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     * @param array $vars An array of module info to update
     * @return array A numerically indexed array of meta fields for the module row containing:
     *    - key The key for this meta field
     *    - value The value for this key
     *    - encrypted Whether or not this field should be encrypted (default 0, not encrypted)
     */
    public function editModuleRow($module_row, array &$vars)
    {
        //define our meta fields
        $meta_fields = array(
            "server_name",
            "host_name",
            "port_number",
            "user_name",
            "password",
            "use_ssl",
            "account_limit",
            "account_count",
            "name_servers"
        );
        //set encrypted fields
        $encrypted_fields = array("user_name", "password");

        // Set unspecified checkboxes
        if (empty($vars['use_ssl']))
            $vars['use_ssl'] = "false";

        $this->Input->setRules($this->getRowRules($vars));

        // Validate module row
        if ($this->Input->validates($vars)) {

            // Build the meta data for this row
            $meta = array();
            foreach ($vars as $key => $value) {

                if (in_array($key, $meta_fields)) {
                    $meta[] = array(
                        'key' => $key,
                        'value' => $value,
                        'encrypted' => in_array($key, $encrypted_fields) ? 1 : 0
                    );
                }
            }

            return $meta;
        }
    }

    /**
     * Deletes the module row on the remote server. Sets Input errors on failure,
     * preventing the row from being deleted.
     *
     * @param stdClass $module_row The stdClass representation of the existing module row
     */
    public function deleteModuleRow($module_row)
    {

    }

    /**
     * Returns all fields to display to an admin attempting to add a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
     */
    //@todo add generate password option
    //@todo add option if server already exists on system
    /**
     * Returns an array of available service delegation order methods. The module
     * will determine how each method is defined. For example, the method "first"
     * may be implemented such that it returns the module row with the least number
     * of services assigned to it.
     *
     * @return array An array of order methods in key/value pairs where the key is the type to be stored for the group and value is the name for that option
     * @see Module::selectModuleRow()
     */
    public function getGroupOrderOptions()
    {

    }

    /**
     * Determines which module row should be attempted when a service is provisioned
     * for the given group based upon the order method set for that group.
     *
     * @return int The module row ID to attempt to add the service with
     * @see Module::getGroupOrderOptions()
     */
    public function selectModuleRow($module_group_id)
    {
        if (!isset($this->ModuleManager))
            Loader::loadModels($this, array("ModuleManager"));

        $group = $this->ModuleManager->getGroup($module_group_id);

        if ($group) {
            switch ($group->add_order) {
                default:
                case "first":

                    foreach ($group->rows as $row) {
                        if ($row->meta->account_limit > (isset($row->meta->account_count) ? $row->meta->account_count : 0))
                            return $row->id;
                    }

                    break;
            }
        }
        return 0;
    }

    /**
     * Returns all fields used when adding/editing a package, including any
     * javascript to execute when the page is rendered with these fields.
     *
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
     */
    public function getPackageFields($vars = null)
    {

        Loader::loadHelpers($this, ['Html']);
        $fields = new ModuleFields();
       
    
        
        // Fetch all packages available for the given server or server group
        $module_row = null;
        if (isset($vars->module_row) && $vars->module_row > 0) {
            $module_row = $this->getModuleRow($vars->module_row);
        } else {
            $rows = $this->getModuleRows();
            if (isset($rows[0])) {
                $module_row = $rows[0];
            }

            unset($rows);
        }
        
        //if virtualmin_package on change display the options for this
       
        //lets get the packages
        $package = $fields->label(Language::_("virtualmin.package_fields.package", true), "virtualmin_package");
       

        $packagesOptions = array();
        
        if ($module_row) {
           
           
            $packages = $this->getVirtualMinPackages($module_row);

            

            if ($packages != false || is_array($packages)) {
                $packagesOptions = $packages['packages'];
                $packagesValues = $packages['values'];
            } else
                $packagesValues = array('error' => true, 'message' => 'Please check <a href=\'{log_link}\'>blesta log </a>, Could not get packages from server.');


        }
        // $plan->attach(
        //     $fields->fieldSelect(
        //         'meta[plan]',
        //         $plans,
        //         $this->Html->ifSet($vars->meta['plan']),
        //         ['id' => 'virtualmin_plan']
        //     )
        // );
       
        //@todo if we don't have any packages then we need to display error
        $package->attach(
            $fields->fieldSelect(
                "meta[package]",
                $packagesOptions,
                $this->Html->ifSet($vars->meta['package']),
                ['id' => "virtualmin_package", 'data' => json_encode($packagesValues)]
            )
        );
        
        $fields->setField($package);

        // TODO Clean up package disaply when selecting package
        $fields->setHtml("
        
			<script type=\"text/javascript\">
			    try{
			        //pass our package data
                    var packages = JSON.parse($('#virtualmin_package').attr('data'));

                    //var edit_package   = $('#edit_package');
                    if (typeof packages.error != 'undefined' && typeof packages.message == 'string'){
                        console.log('appending error');
                        var log_link = top.window.location.href.replace('packages/add','tools/logs');

                        $('#module_row').parent().append('<li><pre>'+packages.message.replace('{log_link}',log_link)+'</pre></li>')
                    }

                    console.log('packages data',packages);
                 }catch(e){}

                $(document).ready(function() {
                    console.log('getPackageFields loaded');

                    $('#virtualmin_package').change(function() {
                        $('#virtualminPackageSettings').empty();
                        //render package settings on page
                         if (typeof packages[$(this).val()] == 'object'){
                             var package_settings = packages[$(this).val()];
                             console.log('value',package_settings);
                             var setting = $.map(package_settings, function(value,name) {
                                return('<li><b>' +name.replace(/\_/g,' ') +'</b> : ' +  value + '</li>');
                            });
                            $('#virtualminPackageSettings').html(setting.join(''));

                            //display the option checkbox for this pacakage
                            console.log('package_settings',package_settings['allowed_features']);
                            var enabled_features = package_settings['allowed_features'][0].split(' ');
                            console.log('enabled_features',enabled_features);

                            $.each(enabled_features,function(index,value){
                                $('.allowed_features.'+value).removeClass('hidden');
                                console.log('value',value);
                            });

                         }
                         //update from preloaded
					});
					 $('#virtualmin_package').trigger('change');
                });
			</script>
			<div id='virtualminPackageSettings'></div>
		");
       
        //@todo wrap the attache into a function we can ref
        //$name, $for=null, array $attributes=null, $preserve_tags=false
        $panel_options = $fields->label(
            "Enable Options.",
            "options",
            array('style' => "font-size:15px")
        );

        
        //default style


        //append default classes
        $theStyle = $this->LabelStyle();
        
        $theStyle['class'] = $theStyle['class'].' mail';
       
        //mail
        $panel_options_mail = $fields->label(Language::_("virtualmin.packages.mail", true), "enable_mail", $theStyle);

        $panel_options->attach(
        //$name, $value=null, $checked=false, $attributes=array(), ModuleField $label=null
            $fields->fieldCheckbox(
                "meta[enable_mail]",
                "enable_mail",
                $this->Html->ifSet($vars->meta['enable_mail'], "enable_mail") == "enable_mail",
                array('id' => "virtualmin_panel_options_mail",
                    'class' => $theStyle['class']),
                $panel_options_mail
            )
        );
        $fields->setField($panel_options);

        $theStyle = $this->LabelStyle();
        $theStyle['class'] = $theStyle['class'].' webmin';

        //webmin
        $panel_options_webmin = $fields->label(Language::_("virtualmin.packages.webmin", true), "enable_webmin", $theStyle);

        $panel_options->attach(
        //$name, $value=null, $checked=false, $attributes=array(), ModuleField $label=null
            $fields->fieldCheckbox(
                "meta[enable_webmin]",
                "enable_webmin",
                $this->Html->ifSet($vars->meta['enable_webmin'], "enable_webmin") == "enable_webmin",
                array('id' => "virtualmin_panel_options_webmin",
                    'class' => $theStyle['class']),
                $panel_options_webmin
            )
        );

        $fields->setField($panel_options_webmin);

        //mysql
        $theStyle = $this->LabelStyle();
        $theStyle['class'] = $theStyle['class'].' mysql';
        $panel_options_database = $fields->label(Language::_("virtualmin.packages.database", true), "enable_database", $theStyle);

        $panel_options->attach(
        //$name, $value=null, $checked=false, $attributes=array(), ModuleField $label=null
            $fields->fieldCheckbox(
                "meta[enable_database]",
                "enable_database",
                $this->Html->ifSet($vars->meta['enable_database'], "enable_database") == "enable_database",
                array('id' => "virtualmin_panel_options_database",
                    'class' => $theStyle['class']),
                $panel_options_database
            )
        );
        $fields->setField($panel_options_database);


        $theStyle = $this->LabelStyle();
        $theStyle['class'] = $theStyle['class'].' scripts';

        $panel_options_scripts = $fields->label(Language::_("virtualmin.packages.scripts", true), "enable_scripts", $theStyle);

        $panel_options->attach(
        //$name, $value=null, $checked=false, $attributes=array(), ModuleField $label=null
            $fields->fieldCheckbox(
                "meta[enable_scripts]",
                "enable_scripts",
                $this->Html->ifSet($vars->meta['enable_scripts'], "enable_scripts") == "enable_scripts",
                array('id' => "virtualmin_panel_options_scripts",
                    'class' => $theStyle['class']),
                $panel_options_scripts
            )
        );
        $fields->setField($panel_options_scripts);

        $theStyle = $this->LabelStyle();
        $theStyle['class'] = $theStyle['class'].' backups';

        $panel_options_backups = $fields->label(Language::_("virtualmin.packages.backups", true), "enable_scripts", $theStyle);

        $panel_options->attach(
        //$name, $value=null, $checked=false, $attributes=array(), ModuleField $label=null
            $fields->fieldCheckbox(
                "meta[enable_backups]",
                "enable_backups",
                $this->Html->ifSet($vars->meta['enable_backups'], "enable_backups") == "enable_backups",
                array('id' => "virtualmin_panel_options_backups",
                    'class' => $theStyle['class']),
                $panel_options_backups
            )
        );
        $fields->setField($panel_options_backups);
        return $fields;

    }

    /**
     *
     * Get the current module row for the selected module group
     * @param null $vars
     * @return null|stdClass module row of current group
     */
    private function getOurModuleRows($vars = null)
    {
        // Fetch all packages available for the given server or server group
        $module_row = null;

        if (isset($vars->module_group) && $vars->module_group == "") {
            if (isset($vars->module_row) && $vars->module_row > 0) {
                $module_row = $this->getModuleRow($vars->module_row);
            } else {
                $rows = $this->getModuleRows();
                if (isset($rows[0]))
                    $module_row = $rows[0];
                unset($rows);
            }
        } else {
            // Fetch the 1st server from the list of servers in the selected group
            $rows = $this->getModuleRows($vars->module_group);

            if (isset($rows[0]))
                $module_row = $rows[0];
            unset($rows);
        }

        return $module_row;
    }

    private function getVirtualMinPackages($module_row)
    {

        try {
            //get the packages from virtualmin
           
            $response = $this->api($module_row)->list_plans();

           
            $this->log($module_row->meta->host_name . "|" . 'getVirtualMinPackages', serialize($response), "output", !empty($response));


            $response = $this->parseResponse($response, $module_row);

            if ($this->Input->errors())
                return false;


            // Packages are set in 'list'
            $plans = (isset($response->data) ? $response->data : array());

            //preset packages array
            $packages = array();
            $packages['packages']['-0'] = "--- SELECT PLAN ----";
            // Assign the key/value for each package
            foreach ($plans as $package => $values) {
                $packages['packages'][$package] = $package;
                $packages['values'][$package] = $values;
            }

            return $packages;
        } catch (Exception $e) {
            // API request failed
            $message = $e->getMessage();
            $this->log($module_row->meta->host_name . "|" . 'getVirtualMinPackages', serialize($message), "output", false);
            return false;
        }
    }

    private function LabelStyle()
    {
        return array(
            'style' => "padding:2px;margin-right:10px",
            'class' => 'allowed_features '
            // TODO fix up hidden features on dropdown change
        );
    }

    /**
     * Returns an array of key values for fields stored for a module, package,
     * and service under this module, used to substitute those keys with their
     * actual module, package, or service meta values in related emails.
     *
     * @return array A multi-dimensional array of key/value pairs where each key is one of 'module', 'package', or 'service' and each value is a numerically indexed array of key values that match meta fields under that category.
     * @see Modules::addModuleRow()
     * @see Modules::editModuleRow()
     * @see Modules::addPackage()
     * @see Modules::editPackage()
     * @see Modules::addService()
     * @see Modules::editService()
     */
    public function getEmailTags()
    {
        if (isset($this->config->email_tags)) {
            return (array)$this->config->email_tags;
        }
        return array();
    }

    /**
     * Returns all tabs to display to a client when managing a service whose
     * package uses this module client tabs
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => title, or method => array where array contains:
     *    - name (required) The name of the link
     *    - icon (optional) use to display a custom icon
     *    - href (optional) use to link to a different URL
     *        Example: array('methodName' => "Title", 'methodName2' => "Title2")
     *        array('methodName' => array('name' => "Title", 'icon' => "icon"))
     */
    //@todo check what tabs the users is allowed to use , possible a pacakge option that first inherits from the virtualmin package then can be selected by the admin

    public function getAdminAddFields($package, $vars = null)
    {
        //generate admin view to add service
        Loader::loadHelpers($this, array("Html"));


        $fields = new ModuleFields();

        // Create domain label
        $domain = $fields->label(Language::_("virtualmin.service_field.domain", true), "virtualmin_domain");

        // Create domain field and attach to domain label
        $domain->attach($fields->fieldText("virtualmin_domain", $this->Html->ifSet($vars->virtualmin_domain, $this->Html->ifSet($vars->domain)), array('id' => "virtualmin_domain")));


        $fields->setField($domain);

        // Create password label
        $password = $fields->label(Language::_("virtualmin.service_field.password", true), "virtualmin_password");

        // Create password field and attach to password label
        $password->attach($fields->fieldText("virtualmin_password", $this->Html->ifSet($vars->virtualmin_password), array('id' => "virtualmin_password")));


        $fields->setField($password);
        unset($domain);
        unset($password);


        return $fields;
    }

    /**
     * Returns all fields to display to a client attempting to add a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
     */
    public function getClientAddFields($package, $vars = null)
    {

        //render client buying options view
        Loader::loadHelpers($this, array("Form", "Html"));

        $fields = new ModuleFields();

        // Create domain label
        $domain = $fields->label(Language::_("virtualmin.service_field.domain", true), "virtualmin_domain");

        // Create domain field and attach to domain label
        $domain->attach($fields->fieldText("virtualmin_domain", $this->Html->ifSet($vars->virtualmin_domain, $this->Html->ifSet($vars->domain)), array('id' => "virtualmin_domain")));


        $fields->setField($domain);

        // Create password label
        $password = $fields->label(Language::_("virtualmin.service_field.password", true), "virtualmin_password");

        // Create password field and attach to password label
        $password->attach($fields->fieldText("virtualmin_password", $this->Html->ifSet($vars->virtualmin_password), array('id' => "virtualmin_password")));

        $fields->setField($password);
        unset($domain);
        unset($password);
        return $fields;
    }

    /**
     * Returns all fields to display to an admin attempting to edit a service with the module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @param $vars stdClass A stdClass object representing a set of post fields
     * @return ModuleFields A ModuleFields object, containg the fields to render as well as any additional HTML markup to include
     */
    public function getAdminEditFields($package, $vars = null)
    {
        return new ModuleFields();
    }

    /**
     * Fetches the HTML content to display when viewing the service info in the
     * admin interface.
     *
     * @param stdClass $service A stdClass object representing the service
     * @param stdClass $package A stdClass object representing the service's package
     * @return string HTML content containing information to display when viewing the service info
     */
    public function getAdminServiceInfo($service, $package)
    {
        return "";
    }

    /**
     * Fetches the HTML content to display when viewing the service info in the
     * client interface.
     *
     * @param stdClass $service A stdClass object representing the service
     * @param stdClass $package A stdClass object representing the service's package
     * @return string HTML content containing information to display when viewing the service info
     */
    public function getClientServiceInfo($service, $package)
    {
        return "";
    }

    /**
     * Returns all tabs to display to an admin when managing a service whose
     * package uses this module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => title. Example: array('methodName' => "Title", 'methodName2' => "Title2")
     */
    public function getAdminTabs($package)
    {
        return array();
    }

    /**
     * Returns all tabs to display to a client when managing a service whose
     * package uses this module
     *
     * @param stdClass $package A stdClass object representing the selected package
     * @return array An array of tabs in the format of method => title, or method => array where array contains:
     *    - name (required) The name of the link
     *    - icon (optional) use to display a custom icon
     *    - href (optional) use to link to a different URL
     *        Example: array('methodName' => "Title", 'methodName2' => "Title2")
     *        array('methodName' => array('name' => "Title", 'icon' => "icon"))
     */
    public function getClientTabs($package)
    {

        $tabs = array(
            'clientTabStatus' => array('name' => Language::_("virtualmin.client.tabs.status.menu", true), 'icon' => "fa fa-columns"),
            'clientTabMail' => array('name' => Language::_("virtualmin.client.tabs.mail.menu", true), 'icon' => "fa fa-envelope-o"),
            'clientTabDatabase' => array('name' => Language::_("virtualmin.client.tabs.database.menu", true), 'icon' => "fa fa-bars"),
            //'clientTabFileMin' => array('name' => Language::_("virtualmin.client.tabs.filemin.menu", true), 'icon' => "fa  fa-file"), //still working on this may have to write custom perl module to enable
            'clientTabScripts' => array('name' => Language::_("virtualmin.client.tabs.scripts.menu", true), 'icon' => "fa  fa-chevron-right"),
            'clientTabBackups' => array('name' => Language::_("virtualmin.client.tabs.backup.menu", true), 'icon' => "fa fa-download"),
        );

        if (!isset($package->meta->enable_database)) unset($tabs['clientTabDatabase']);
        if (!isset($package->meta->enable_mail)) unset($tabs['clientTabMail']);
        if (!isset($package->meta->enable_scripts)) unset($tabs['clientTabScripts']);
        if (!isset($package->meta->enable_backups)) unset($tabs['clientTabBackups']);
        return $tabs;
    }

    /**
     *  client Tab FileMin handles filemanager for virtualmin server
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function clientTabFileMin($package, $service, array $getRequest = null, array $postRequest = null, array $files = null)
    {
        /**
         * Going to have to do a custom perl module for this
         */
        //check service is active
        if (($service_active = $this->serviceCheck($service)) !== true)
            return $service_active;

        return "Not available in this release";


        //get the service
        $service_fields = $this->serviceFieldsToObject($service->fields);

        //retrieve domain info
        $account = array('domain' => $service_fields->virtualmin_domain);

        $serverDetails = (object)$this->getVirtualMinHelper()->cleanArray($this->api()->get_domain_info($account))[0];

        //build vars to parse to view
        $module_row = $this->getModuleRow($package->module_row);

        $webmin_url = ((($module_row->meta->use_ssl == "true") ? "https://" : "http://") . $module_row->meta->host_name . ":" . $module_row->meta->port_number);

        if ($serverDetails->password_storage == "Plain text" && strpos($serverDetails->allowed_features, 'webmin') !== false) {

            return sprintf('<form id="filemin" action="%s/session_login.cgi?page=/filemin" method="post" target="_blank">' .
                '<input type="hidden" name="user" value="%s" />' .
                '<input type="hidden" name="pass" value="%s" />' .
                '<input type="hidden" name="notestingcookie" value="1" />' .
                '<input type="hidden" name="page" value="filemin" />' .
                '<script>
                $(document).ready(function(){
                    $("#filemin").submit()
                });
                </script>',
                //'<input type="submit" value="%s" class="btn btn-success" />' . '</form>',
                $webmin_url,
                $serverDetails->username,
                $serverDetails->password
            //$this->_("virtualmin.client.tabs.status.login_webmin",true)
            );
        } else
            return "Not available in this release";

    }

    /**
     * Checks service status and returns true else returns service error message
     *
     * @param $service
     * @return bool true or system generated messaging
     */
    private function serviceCheck($service)
    {
        //if service is not active lets show a error|status message
        if ($service->status != "active") {
            Loader::loadHelpers($this, array("Html"));
            return $this->Html->safe("<h3>" . Language::_("virtualmin.service_field.not_active", true) . "</h3>", true);
        }

        return true;
    }

    /**
     *  client Tab Scripts handles all available scripts that can be installed on client
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function clientTabScripts($package, $service, array $getRequest = null, array $postRequest = null, array $files = null)
    {
        //check service is active
        if (($service_active = $this->serviceCheck($service)) !== true)
            return $service_active;

        //return as not available in this release
        //return "Not available in this release";

        //get the service
        $service_fields = $this->serviceFieldsToObject($service->fields);

        //get the list of current installed scripts
        $account = array('domain' => $service_fields->virtualmin_domain);
        $response = $this->parseResponse($this->api()->list_scripts($account));


        //check errors before any other server requests
        if ($this->Input->errors())
            return;

        //current scripts
        $installed_scripts = array();
        $installed = $this->getVirtualMinHelper()->cleanArray($response);
        

        //store the script type
        if (count($installed) > 1)
        foreach ($installed as $script)
            $installed_scripts[] = $script['type'];

        //lets get list_available_scripts the server has allowed us to install
        $response = $this->parseResponse($this->api()->list_available_scripts());

        //check errors before any other server requests
        if ($this->Input->errors())
            return;

        //clean up our script
        $script_list = $this->getVirtualMinHelper()->cleanArray($response);


        //lets build vars before render
        $buildVars = array(
            "script_list" => $script_list,
            "installed_scripts" => array_flip($installed_scripts),
            "action_url" => $this->base_uri . "services/manage/" . $service->id . "/clientTabScripts/",
            "service_fields" => $service_fields,
            "service_id" => $service->id,
            "vars", (isset($vars) ? $vars : new stdClass())
        );

        //build page
        return $this->renderTemplate("client_tab_scripts", $buildVars);

    }

    /**
     * Initializes the virtualMinLib and returns an instance of that object
     * see lib/virtualmin_lib_helper.php
     * @return The virtualMinLib instance
     */
    private function getVirtualMinHelper()
    {
        if (!$this->_virtualmin_lib_helper) {
            Loader::load(dirname(__FILE__) . DS . "lib" . DS . "virtualmin_lib_helper.php");
            $this->_virtualmin_lib_helper = new virtualmin_lib_helper();
        }

        return new $this->_virtualmin_lib_helper;
    }

    /**
     *
     * Helps renders a template view from an action in our controller
     *
     * @param $templateName     template we want to render to view
     * @param $vars $vars we want to pass to view
     * @param array $helpers optional array of helpers , form & html are default
     * @return string           returns template view
     */
    private function renderTemplate($templateName, $vars, $helpers = array("Form", "Html"))
    {
        //create view
        $this->view = new View($templateName, "default");
        //load helpers
        Loader::loadHelpers($this, $helpers);
        //set base
        $this->view->base_uri = $this->base_uri;
        //set location
        $this->view->setDefaultView("components" . DS . "modules" . DS . "virtualmin_blesta" . DS);

        //pass on our vars to template
        foreach ($vars as $key => $value) {
            $this->view->set($key, $value);
        }

        //return rendered template
        return $this->view->fetch();
    }

    /**
     *  client Tab Backups handles all available backups on virtual server for client
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function clientTabBackups($package, $service, array $getRequest = null, array $postRequest = null, array $files = null)
    {
        //check service is active
        if (($service_active = $this->serviceCheck($service)) !== true)
            return $service_active;

        //get the service
        $service_fields = $this->serviceFieldsToObject($service->fields);


        return "Not available in this release";
    }

    /**
     * Fetches a listing of all packages configured in VirtualMin for the given server
     *
     * @param stdClass $module_row A stdClass object representing a single server
     * @param string $command The API command to call, either getPackagesUser, or getPackagesReseller
     * @return array An array of packages in key/value pairs
     */
    //@todo cleanup packahge plans
    //@todo store what the package allows user to do so we can turn off some functions on billing system from showing

    /**
     *  client Tab Database handles all the database listings and manages databases for client
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function clientTabDatabase($package, $service, array $getRequest = null, array $postRequest = null, array $files = null)
    {
        //check service is active
        if (($service_active = $this->serviceCheck($service)) !== true)
            return $service_active;

        //allowed request to clientTabDatabase
        $allowedRequests = array("add_database", "delete_database");
        $dataRequest = array(
            'package' => $package,
            'service' => $service,
        );
        //process any ajax request first
        $this->getVirtualMinHelper()->processAjax($this, $getRequest, $postRequest, $allowedRequests, $dataRequest);

        //get the service
        $service_fields = $this->serviceFieldsToObject($service->fields);

        //get the databases for this account
        $account = array('domain' => $service_fields->virtualmin_domain);
        $databases = $this->api()->list_databases($account);


        //lets build vars before render
        $buildVars = array(
            "databases" => $databases->data,
            "action_url" => $this->base_uri . "services/manage/" . $service->id . "/clientTabDatabase/",
            "service_fields" => $service_fields,
            "service_id" => $service->id,
            //"confirm"				 => $this->view->fetch("client_dialog_confirm"),
            //"action_buttons" => $this->clientActionButtons(),
            "vars", (isset($vars) ? $vars : new stdClass())
        );

        //build page

        return $this->renderTemplate("client_tab_database", $buildVars);
    }

    /**
     * client Tab Mail handles all the mail from listing to adding and deleting mailboxs
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function clientTabMail($package, $service, array $getRequest = null, array $postRequest = null, array $files = null)
    {

        //check service is active
        if (($service_active = $this->serviceCheck($service)) !== true)
            return $service_active;

        //allowed request to clientTabMail
        $allowedRequests = array("add_mail_account", "mail_delete_user", "mail_change_password");
        $dataRequest = array(
            'package' => $package,
            'service' => $service,
        );

        //process any ajax request first
        $this->getVirtualMinHelper()->processAjax($this, $getRequest, $postRequest, $allowedRequests, $dataRequest);


        //get the service
        $service_fields = $this->serviceFieldsToObject($service->fields);

        //pass this account to api
        $account = array('domain' => $service_fields->virtualmin_domain);

        //grab the mailAccounts for domain
        $mail_accounts = $this->getVirtualMinHelper()->cleanArray($this->api()->list_users($account));
       
        //lets build vars before render
        $buildVars = array(
            "mail_accounts" => $mail_accounts,
            "action_url" => $this->base_uri . "services/manage/" . $service->id . "/clientTabMail/",
            "service_fields" => $service_fields,
            "service_id" => $service->id,
            //"confirm"				 => $this->view->fetch("client_dialog_confirm"),
            //"action_buttons" => $this->clientActionButtons(),
            "vars", (isset($vars) ? $vars : new stdClass())
        );

        if (!empty($postRequest)) {


            //lets checks some posts out
            //virtualmin_confirm_password
            /*
            $data = array(
                "virtualmin_action" 		=> $postRequest["action"] ,
                "virtualmin_add_mail_username" 	=> $postRequest["add_mail_username"],
                "virtualmin_add_mail_password"	=> $postRequest["add_mail_password"],
                "virtualmin_add_mail_quota"		=> $postRequest["add_mail_quota"]
            );



            $this->Input->setRules($this->addMailAccountRules($data));
            $this->Input->validates($data);



            $vars = (object)$data;
            //update page $vars
            $buildVars["vars"] = $vars;*/
        }

        return $this->renderTemplate("client_tab_mail", $buildVars);


    }

    /**
     * client Tab Status provides details based on the virtual server
     *
     * @param stdClass $package A stdClass object representing the current package
     * @param stdClass $service A stdClass object representing the current service
     * @param array $get Any GET parameters
     * @param array $post Any POST parameters
     * @param array $files Any FILES parameters
     * @return string The string representing the contents of this tab
     */
    public function clientTabStatus($package, $service, array $getRequest = null, array $post = null, array $files = null)
    {


        //check if the service is currently active
        if (($service_active = $this->serviceCheck($service)) !== true)
            return $service_active;

        //set out view
        $current_view = 'client_tab_status';

        //clear current session while testing
        $this->api()->clearSession();

        //parse service fields
        $service_fields = $this->serviceFieldsToObject($service->fields);

        //retrieve domain info
        $account = array('domain' => $service_fields->virtualmin_domain);

        $serverDetails = $this->getVirtualMinHelper()->cleanArray($this->api()->get_domain_info($account));


        //build vars to parse to view
        $module_row = $this->getModuleRow($package->module_row);
        //get host
        //^(?:https?:\/\/)?(?:[^@\n]+@)?(?:www\.)?([^:\/\n]+)
        //lets just get the host

        $buildVars = array(
            "serverDetails" => (object)$serverDetails[0],
            "hostname" => $service_fields->virtualmin_domain,
            "action_url" => $this->base_uri . "services/manage/" . $service->id . "/clientTabStatus/",
            "service_fields" => $service_fields,
            "service_id" => $service->id,
            "name_servers" => $module_row->meta->name_servers,
            "webmin_access" => (strpos($serverDetails[0]['allowed_features'], 'webmin') !== false && isset($package->meta->enable_webmin)),
            "webmin_url" => ((($module_row->meta->use_ssl == "true") ? "https://" : "http://") . $module_row->meta->host_name . ":" . $module_row->meta->port_number),
            //"action_buttons" => $this->clientActionButtons(),
            "vars", (isset($vars) ? $vars : new stdClass())
        );


        // Perform any post request based on get request
        if (!empty($post)) {

        }


        //render template
        return $this->renderTemplate($current_view, $buildVars);
    }

    /**
     * Validates port number is numeric
     * @param $port_number
     * @return bool
     */
    public function validatePortNumber($port_number)
    {
        return is_numeric($port_number);
    }

    /**
     * Validates that at least 2 name servers are set in the given array of name servers
     *
     * @param array $name_servers An array of name servers
     * @return boolean True if the array count is >=2, false otherwise
     */
    public function validateNameServerCount($name_servers)
    {
        if (is_array($name_servers) && count($name_servers) >= 2)
            return true;
        return false;
    }

    /**
     * Validates that the nameservers given are formatted correctly
     *
     * @param array $name_servers An array of name servers
     * @return boolean True if every name server is formatted correctly, false otherwise
     */
    public function validateNameServers($name_servers)
    {
        if (is_array($name_servers)) {
            foreach ($name_servers as $name_server) {
                if (!$this->validateHostName($name_server))
                    return false;
            }
        }
        return true;
    }

    /**
     * Validates that the given hostname is valid
     *
     * @param string $host_name The host name to validate
     * @return boolean True if the hostname is valid, false otherwise
     */
    public function validateHostName($host_name)
    {
        if (strlen($host_name) > 255)
            return false;

        return $this->Input->matches($host_name, "/^([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9])(\.([a-z0-9]|[a-z0-9][a-z0-9\-]{0,61}[a-z0-9]))*$/");
    }

    /**
     * Changes a password for mail account via Ajax for our Client Mail Tab
     *
     * @param $postRequest is the post passed by client
     * @param array $dataRequest is an array of the service & package
     *
     */
    public function mail_change_password($postRequest, $dataRequest = array())
    {
        //get the email user account from the id

        $email_address = $postRequest["email_address"];
        $new_password = $postRequest["new_password"];

        //lets validate our rules against posts before anything else gets done
        $this->Input->setRules($this->changeMailAccountRules($postRequest));
        $this->Input->validates($postRequest);


        //pass issues back to client
        if ($errors = $this->Input->errors()) {
            $this->getVirtualMinHelper()->sendAjax($errors, false);
        }
        //parse service & package
        $service = $dataRequest['service'];
        $package = $dataRequest['package'];


        //grab service details
        $service_fields = $this->serviceFieldsToObject($service->fields);

        //pass account and user we want to change
        $change_password = array(
            'domain' => $service_fields->virtualmin_domain,
            'user' => str_replace('@' . $service_fields->virtualmin_domain, "", $email_address),
            'pass' => $new_password
        );

        //send changepassword request.
        $response = $this->parseResponse($this->api()->modify_user($change_password));

        //check for errors.
        if ($errors = $this->Input->errors()) {
            $this->getVirtualMinHelper()->sendAjax($response, false);
        }

        //$api->clearSession("list-users");
        $this->getVirtualMinHelper()->sendAjax($response->output);

    }

    /**
     * Builds and returns the rules for changing password
     *
     * @param array $vars An array of key/value data pairs
     * @return array An array of Input rules suitable for Input::setRules()
     */
    public function changeMailAccountRules(&$vars)
    {
        return array(
            'new_password' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_('virtualmin.!error.password.format', true)
                ),
                'valid' => array(
                    'rule' => array("matches", "/^[(\x20-\x7F)]*$/"), // ASCII 32-127,
                    'message' => Language::_('virtualmin.!error.virtualmin_password.length', true)
                )
            )
        );
    }

    /**
     * Manages adding a new mail account via Ajax for our Client Mail Tab
     *
     * @param $postRequest is the post passed by client
     * @param array $dataRequest is an array of the service & package
     * @throws Exception
     */
    public function add_mail_account($postRequest, $dataRequest = array())
    {

        //set our data & required post
        $vars = array(
            "virtualmin_action" => $postRequest["action"],
            "virtualmin_add_mail_username" => $postRequest["add_mail_username"],
            "virtualmin_add_mail_password" => $postRequest["add_mail_password"],
            "virtualmin_add_mail_quota" => $postRequest["add_mail_quota"],
            "virtualmin_enable_mail_forward" => $postRequest["enable_mail_forward"],
            "virtualmin_email_forward_to" => $postRequest["email_forward_to"]
        );

        //lets validate our rules
        $this->Input->setRules($this->addMailAccountRules($vars));
        $this->Input->validates($vars);


        //validate rules before heading to editService
        if ($errors = $this->Input->errors()) {
            $response["errors"] = $errors;
            $response["message"] = "failed on validation";
            $this->getVirtualMinHelper()->sendAjax($response, false);
        }

        //lets grab the service
        $service = $dataRequest['service'];

        Loader::loadModels($this, array("Services"));
        $this->Services->edit($service->id, $vars);
        //lets make sure there is no issues between query and service
        if ($errors = $this->Services->errors()) {
            //$this->Input->setErrors($this->Services->errors());
            $this->getVirtualMinHelper()->sendAjax($errors);
        }

        //lets create the mail account on server
        $service_fields = $this->serviceFieldsToObject($service->fields);


        $account = array(
            'domain' => $service_fields->virtualmin_domain,
            'user' => $vars["virtualmin_add_mail_username"],
            'pass' => $vars["virtualmin_add_mail_password"]
        );
        /*
         * [status] => success
            [output] => User test.testing1.com created successfully
            [command] => create-user
         */
        $response = $this->parseResponse($this->api()->create_user($account));

        if ($errors = $this->Input->errors()) {
            $this->getVirtualMinHelper()->sendAjax($errors, false);
        }
        //clear the list-users
        //$api->clearSession("list-users");
        $this->getVirtualMinHelper()->sendAjax($response->output);

    }

    /**
     * Builds and returns the rules for add_mail_account
     *
     * @param array $vars An array of key/value data pairs
     * @return array An array of Input rules suitable for Input::setRules()
     */
    public function addMailAccountRules(&$vars)
    {
        return array(
            'virtualmin_add_mail_password' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_('virtualmin.!error.password.format', true)
                ),
                'valid' => array(
                    'rule' => array("matches", "/^[(\x20-\x7F)]*$/"), // ASCII 32-127,
                    'message' => Language::_('virtualmin.!error.virtualmin_password.length', true)
                )
            ),
            'virtualmin_add_mail_username' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_('virtualmin.!error.user_name.empty', true)
                ),
                'valid' => array(
                    'rule' => array("matches", "/^[a-z0-9]*$/i"),
                    'message' => Language::_('virtualmin.!error.virtualmin_username.format', true)
                )
            ),
            'virtualmin_add_mail_quota' => array(
                'format' => array(
                    'if_set' => true,
                    'rule' => array("matches", "/^[0-9]*$/i"),
                    'message' => Language::_("virtualmin.!error.mail_account.quota", true)
                )
            )
        );
    }

    /**
     * Deletes a mail account via Ajax for our Client Mail Tab
     *
     * @param $postRequest is the post passed by client
     * @param array $dataRequest is an array of the service & package
     * @throws Exception
     */
    public function mail_delete_user($postRequest, $dataRequest = array())
    {

        //get the email user account from the id
        $email_id = $postRequest['email_id'];
        $email_address = $postRequest["email_address"];

        //parse service & package
        $service = $dataRequest['service'];
        $package = $dataRequest['package'];

        //grab service details
        $service_fields = $this->serviceFieldsToObject($service->fields);

        //get the mail accounts for domain
        $account = array('domain' => $service_fields->virtualmin_domain);
        $mail_accounts = $this->api()->list_users($account);

        //grab the users account we are referencing
        //check that the index passed is not more than our array
        if ($email_id > count($mail_accounts)) {
            $this->log(
                $service_fields->virtualmin_domain . "| delete_user account failed mailAccount[$email_id] is out of bounds" .
                serialize(array($service_fields->virtualmin_username, $package->meta->package)
                ), "output", true
            );
            //send error as email_id doesn't match account for that domain
            $this->getVirtualMinHelper()->sendAjax("Incorrect email details", false);
        }

        //grab the user account
        $user_account = $mail_accounts->data[$email_id]->values;

        //make sure that the email matches the users_accounts email that we are wanting to delete
        if ($user_account->email_address[0] != $email_address) {
            $this->log(
                $service_fields->virtualmin_domain . "| delete_user account failed [$email_address] does not match" .
                serialize(array($service_fields->virtualmin_username, $package->meta->package)
                ), "output", true
            );
            $this->getVirtualMinHelper()->sendAjax("Incorrect email details", false);
        }

        //	->unix_username[0];
        //prepare to delete the user from account
        $account['user'] = $user_account->unix_username[0];

        //call api
        $response = $this->parseResponse($this->api()->delete_user($account));
        if ($errors = $this->Input->errors()) {
            $this->getVirtualMinHelper()->sendAjax($errors, false);
        }
        //clear the list-users
        //$api->clearSession("list-users");
        $this->getVirtualMinHelper()->sendAjax($response->output);


    }

    /**
     * Adds a database from VirtualMin server
     *
     * @param $postRequest is the post passed by client
     * @param array $dataRequest is an array of the service & package
     * @throws Exception
     */
    public function add_database($postRequest, $dataRequest = array())
    {

        //get the email user account from the id
        $database_name = $postRequest['database_name'];
        $database_type = 'mysql';  //@todo possible support other

        //parse service & package
        $service = $dataRequest['service'];
        $package = $dataRequest['package'];

        //grab service details
        $service_fields = $this->serviceFieldsToObject($service->fields);


        //lets validate our rules against posts
        $this->Input->setRules($this->databaseParseRules($postRequest));
        $this->Input->validates($postRequest);


        //validate rules before processing the request
        if ($errors = $this->Input->errors()) {
            $response["errors"] = $errors;
            $response["message"] = "failed on validation";
            $this->getVirtualMinHelper()->sendAjax($response, false);
        }

        //clear database session as we are now processing a request
        $this->api()->clearSession();

        //lets create the database
        $prams = array(
            'domain' => $service_fields->virtualmin_domain,
            'name' => $database_name,
            'type' => $database_type
        );

        //check for issues
        $database_response = $this->parseResponse($this->api()->create_database($prams));

        //pass issues back to client
        if ($errors = $this->Input->errors()) {
            $this->getVirtualMinHelper()->sendAjax($errors, false);
        }

        //database added send back
        $this->getVirtualMinHelper()->sendAjax($database_response->output);

    }

    /**
     * Builds and returns the rules for add_mail_account
     *
     * @param array $vars An array of key/value data pairs
     * @return array An array of Input rules suitable for Input::setRules()
     */
    public function databaseParseRules(&$vars)
    {
        return array(
            'database_name' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_('virtualmin.client.tabs.database.database_name.empty', true)
                )),
            'action' => array(
                'empty' => array(
                    'rule' => "isEmpty",
                    'negate' => true,
                    'message' => Language::_("virtualmin.!error.action.empty", true)
                )
            )
        );
    }

    /**
     * Deletes a database from VirtualMin server
     *
     * @param $postRequest is the post passed by client
     * @param array $dataRequest is an array of the service & package
     * @throws Exception
     */
    public function delete_database($postRequest, $dataRequest = array())
    {

        //get the email user account from the id
        $database_name = $postRequest['database_name'];
        $database_type = 'mysql';  //@todo possible support other

        //parse service & package
        $service = $dataRequest['service'];
        $package = $dataRequest['package'];

        //grab service details
        $service_fields = $this->serviceFieldsToObject($service->fields);


        //lets validate our rules against posts
        $this->Input->setRules($this->databaseParseRules($postRequest));
        $this->Input->validates($postRequest);

        //validate rules before processing the request
        if ($errors = $this->Input->errors()) {
            $response["errors"] = $errors;
            $response["message"] = "failed on validation";
            $this->getVirtualMinHelper()->sendAjax($response, false);
        }

        //delete_database
        //lets create the database
        $prams = array(
            'domain' => $service_fields->virtualmin_domain,
            'name' => $database_name,
            'type' => $database_type
        );

        //clear database session as we have modified session data
        $this->api()->clearSession();

        $database_response = $this->parseResponse($this->api()->delete_database($prams));

        //pass issues back to client
        if ($errors = $this->Input->errors()) {
            $this->getVirtualMinHelper()->sendAjax($errors, false);
        }

        //database added send back
        $this->getVirtualMinHelper()->sendAjax($database_response->output);
    }

    /**
     * Returns a safe username from a domain name (not using for now)
     *
     * @param $domain domain name or possible url
     * @return string safe string as keycdn name as refrence
     */
    private function usernameFromDomain($domain)
    {
        //generate name
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace("/[^A-Za-z0-9 ]/", '', $domain);

        return trim($domain);
    }


}