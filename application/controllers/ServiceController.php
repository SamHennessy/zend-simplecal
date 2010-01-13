<?php
/**
 * ServiceController
 */
require_once 'Zend/Controller/Action.php';
class ServiceController extends Zend_Controller_Action
{
    /**
     * The default action - show the home page
     */
    public function emailAction ()
    {
        $params = ZendJobQueue::getCurrentJobParams();

        $this->_validateRequest($params);

        $mail = new Zend_Mail();
        $mail->setBodyText($params['body']);
        $mail->setFrom('somebody@example.com', 'SimpleCal Admin');
        $mail->addTo($params['to']);
        $mail->setSubject($params['subject']);
        //$mail->send();

        $this->_helper->viewRenderer->setNoRender();
    }

    private function _validateRequest($params)
    {
        if(isset($params['password']) == false
            || $params['password'] != '238ddae81c627af85737fa82cebfe885')
        {
            exit('Terminated, due to an error!');
        }
    }
}

