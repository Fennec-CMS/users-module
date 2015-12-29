<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 ************************************************************************
 */
namespace Fennec\Modules\Users\Controller;

use \Fennec\Controller\Base;
use \Fennec\Modules\Users\Model\User as UserModel;

/**
 * Users module
 *
 * @author David Lima
 * @version 1.0
 */
class Index extends Base
{

    /**
     * User Model
     *
     * @var \Fennec\Modules\Users\Model\User
     */
    private $model;

    /**
     * Defines $this->model
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->model = new UserModel();
    }

    public function loginAction()
    {
        if ($this->isPost()) {
            $this->model->setUsername($this->getPost('username'));
            $this->model->setPassword($this->getPost('password'));
            $this->model->authenticate();
            
            if (! $this->isAuthenticated()) {
                $this->errors = array(
                    $this->translate('Cannot authenticate you')
                );
            }
        }
        
        if ($this->isAuthenticated()) {
            header("Location: " . $this->linkToRoute('user-profile'));
        }
    }

    public function registerAction()
    {
        if ($this->isPost()) {
            try {
                foreach ($this->getPost() as $postKey => $postValue) {
                    $this->$postKey = $postValue;
                }
                
                $this->model->setName($this->getPost('name'));
                $this->model->setUsername($this->getPost('username'));
                $this->model->setEmail($this->getPost('email'));
                $this->model->setPassword($this->getPost('password'));
                $this->model->setStatus(0);
                
                $this->result = $this->model->create();
                if (isset($this->result['errors'])) {
                    $this->result['result'] = implode('<br>', $this->result['errors']);
                }
            } catch (\Exception $e) {
                $this->exception = $e;
                $this->throwHttpError(500);
            }
        }
    }
    
    public function profileAction()
    {
        if (! $this->isAuthenticated()) {
            header("Location: " . $this->linkToRoute('users-authenticate'));
        }
    }

    /**
     * Check if there is an user authenticated
     *
     * @return boolean
     */
    private function isAuthenticated()
    {
        return (isset($_SESSION['fennecUser']) && $_SESSION['fennecUser'] instanceof \Fennec\Modules\Users\Model\User);
    }
}
