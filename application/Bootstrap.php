<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initRequest(array $options = array())
    {
        // Ensure front controller instance is present, and fetch it
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController'); /* @var $front Zend_Controller_Front */
        
        // Set up routes
        $front->getRouter()->addConfig(
            new Zend_Config(include APPLICATION_PATH . '/configs/routes.php')
        );
        
        // Initialize and set the request object
        $request = new Zend_Controller_Request_Http();
        $front->setRequest($request);
        
        // Bootstrap will store this value in the 'request' key of its container
        return $request;
    }
    
    protected function _initResponse()
    {
        // Ensure front controller instance is present, and fetch it
        $this->bootstrap('FrontController');
        $front = $this->getResource('FrontController'); /* @var $front Zend_Controller_Front */
        
        // Initialize and set the response object
        $response = new Zend_Controller_Response_Http();
        $response->setHeader('Content-Type', 'text/html; charset=UTF-8', true);
        $front->setResponse($response);
        
        return $response;
    }
    
    protected function _initView()
    {
        $this->bootstrap(array('request'));
        
        // Initialize view
        $view = new Zend_View();
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');
        $view->headTitle()->setSeparator(' » ');

        // Save the base URL
        $view->baseUrl = $this->getResource('request')->getBaseUrl();
        
        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);
        
        // Add some stylesheet
        $view->headLink()->appendStylesheet($view->baseUrl . '/css/default.css');

        // Set user info
        /*
        $session = $this->getResource('session');
        $view->userLoggedIn = $session->logged_in;
        $view->userInfo = $session->user;
        */
        
        $view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'SimpleCal_View_Helper_');
        Zend_Dojo::enableView($view);
        
        // Return it, so that it can be stored by the bootstrap
        return $view;
    }
    
    protected function _initAutoloader()
    {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'SimpleCal_',
            'basePath'  => APPLICATION_PATH
        ));
        return $autoloader;
    }
}   