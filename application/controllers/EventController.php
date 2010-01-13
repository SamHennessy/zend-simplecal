<?php

class EventController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    public function createAction()
    {
        $form = new SimpleCal_Form_CreateEvent(array(
            'action' => $this->view->baseUrl . '/event/create'
        ));
        
        $form->populate($this->_getAllParams());
        
        if ($this->getRequest()->isPost()) {
            $params = $this->_getAllParams();
            if ($form->isValid($params)) {
                
                $startTime = strtotime($form->getValue('date') . " " . $form->getValue('time'));
                
                $event = new SimpleCal_Model_Event(array(
                    'start_time'  => $startTime,
                    'end_time'    => $startTime + 1800, // TODO: Implement this!
                    'title'       => $form->getValue('title'),
                    'description' => $form->getValue('description')
                ));

                $emailArray = $this->_processEmailString($form->getValue('invite'));

                if(count($emailArray))
                {
                    $this->_sendInvites($emailArray, $event);
                }

                $event->save();

                $month = date("Y/m", $event->getStartTime());
                if ($month) {
                    $this->_redirect("/month/$month");
                } else {
                    $this->_redirect('/');
                }
            }
        }
        
        $this->view->form = $form;
    }

    private function _processEmailString($emailString)
    {
        $parts = explode(',', $emailString);
        $parts = array_map('trim', $parts);

        $validator = new Zend_Validate_EmailAddress();
        $vaildEmailArray = array();
        foreach($parts as $emailAddress)
        {
            if($validator->isValid($emailAddress))
            {
                $vaildEmailArray[] = $emailAddress;
            }
        }
        return $vaildEmailArray;
    }

    private function _sendInvites($emailArray, SimpleCal_Model_Event $event)
    {
        $uri = Zend_Uri::factory($_SERVER['HTTP_HOST']);

        exit($uri->getUri());

        $baseUrlHelper = new Zend_View_Helper_BaseUrl();
exit($this->getFrontController()->getBaseUrl());
        $view = new Zend_View(array('basePath' => APPLICATION_PATH . '/views'));
        $viewScript = 'emails/invite.phtml';
        $view->event = $event;

        $queue = new ZendJobQueue();
        $jobUrl = $baseUrlHelper->baseUrl('/service/email');
        //$this->getRequest()->get
        //$this->

        foreach($emailArray as $email)
        {
            $invitePath = $this->_helper->url(
            	'index',
            	'invite',
            	'default',
                array(
                	'passkey'=> urlencode(md5(base64_encode($email + '12wefrguygtf'))),
                	'event_id' => $event->getId()));

            $view->inviteUrl = $baseUrlHelper->baseUrl($invitePath);
            $body = $view->render($viewScript);

            $queue->createHttpJob(
    			$jobUrl,
                array(
                	'password' => '238ddae81c627af85737fa82cebfe885',
                    'subject' => 'Your Invitation',
                    'body' => $body,
                    'to' => $email));
        }
    }
}
