<?php
/**
 * Created by PhpStorm.
 * User: Luke Hardiman
 * Date: 6/07/2015
 * Time: 12:25 AM
 
  @description virtualmin library helper class helps main virtualmin plugin class.
 */

class virtualmin_lib_helper {

    /**
     * Sends back a JSON response with|without callback and exits execution
     *
     * @param $data
     * @param bool $success'
     */
    public function sendAjax($data, $success=true){
        //dump out json with possible callback
        $response = json_encode(
            array(
                "data"		=>	$data,
                "success"	=>	$success
            )
        );

        //$response = json_encode($response);

        echo isset($_GET['callback'])
            ? "{$_GET['callback']}($response)"
            : $response;

        exit(0);
    }

    /**
     * This will loop the $get request and check if the $get is a tab that is in theTab array
     *
     * @param $getRequest
     * @param $request
     * @return bool
     */
    public function isGetRequest($getRequest, $allowedRequests = array()){

        //only check get request if more than 2 else waist of time checking just throw default tab
        if (($counter = count($getRequest)) > 2 &&  count($allowedRequests) >= 1){

                foreach($getRequest as $key => $value){
                    if (in_array($getRequest[$key], $allowedRequests))
                        return $getRequest[$key];
                }

        }

        return false;
    }

    /**
     * Checks if we have a xml request
     * @return bool
     */
    public function isAjax(){
      return  (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * Process ajax's call's only if the request is allowed calls method on main virtualmin by ref
     *
     * @param $caller
     * @param $getRequest
     * @param $postRequest
     * @param $allowedRequest
     */
    public function processAjax(&$caller,$getRequest,$postRequest,$allowedRequest,$dataRequest = array()){
        //all our ajax calls will be post for data and method calls by get
        if ($this->isAjax()){
            //get the request made and can only be request that are in our allowedrequest array
            $request = $this->isGetRequest($getRequest,$allowedRequest);
            //check that this allowed request is callable
            if (method_exists($caller,$request) && is_callable(array($caller, $request)))
                $caller->$request($postRequest,$dataRequest);

            //invalid call
            $this->sendAjax("Invalid call to $request",false);

        }
    }

    /**
     * Cleans up the data back from virutlamin server more readable array like maparray
     *
     * @param $arrayData    mixed array|multi array
     * @return object| bool   Returns a virtualmin clean object with values
     */
    public function cleanArray($arrayData, $useResultName = false){
        $cleanArray = [];
        if (isset($arrayData->data) && isset($arrayData->data[0]->values)){
                foreach ($arrayData->data as $index => $results) {

                foreach($results->values as $key => $values ){
                    //
                    // print_r($results->values->unix_username[0]);exit;
                    if (count($values) > 1){
                        //remove unwanted forward address
                        if ($key == 'forward_mail_to'){
                            $adminRedirect = str_replace('@','-','\\'.$results->values->unix_username[0]);
                            $forward_mail_to = [];
                            foreach($values as $email){
                           
                                if ($email == $adminRedirect) continue;
                               
                                $forward_mail_to[] = $email;
                            }
                            $cleanArray[$index][$key] = $forward_mail_to;
                        }else{
                           
                            if (isset($results->name) && !empty($results->name) &&  $useResultName == true){
                                $cleanArray[$results->name][$key] = $values;
                            }else{
                                $cleanArray[$index][$key] = $values;
                            }

                        }
                       

                    }      //if there is possible multiple values we will then store them as
                       
                    else{
                        
                      
                        if (isset($results->name) && !empty($results->name) &&  $useResultName == true){
                            $cleanArray[$results->name][$key] = $values[0];
                        }else{
                            $cleanArray[$index][$key] = $values[0];
                        }
                     
                    }
                       
                }



                }

        }

        unset($data); // or $mainArr = $resultArr;
        return (!empty($cleanArray) && count($cleanArray) > 0) ? (array)$cleanArray : false;
    }
}