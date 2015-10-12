<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 ************************************************************************
 */
namespace Fennec\Modules\Users\Controller\Admin;

use \Fennec\Controller\Admin\Index as AdminController;
use \Fennec\Modules\Users\Model\User as UserModel;

/**
 * Sample custom module (Admin controller)
 *
 * @author David Lima
 * @version 1.0
 */
class Index extends AdminController
{
    /**
     * User model
     * @var \Fennec\Modules\Users\Model\User
     */
    private $model;
    
    /**
     * Initial setup
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new UserModel();
        

        $this->moduleInfo = array(
            'title' => 'Users'
        );
    }

    /**
     * Default action
     */
    public function indexAction()
    {
        $this->list = $this->model->getAll()->fetchAll();
    }

    /**
     * If is a POST, try to save a new (or edit a) post. Show form otherwise
     */
    public function createAction()
    {
        if ($this->getParam('id')) {
            $id = $this->model->id = (int) $this->getParam('id');
            $post = $this->model->getByColumn('id', $id);
            if (count($post)) {
                $this->post = $post[0];
                foreach($this->post as $param => $value){
                    $this->$param = $value;
                }
            } else {
                $link = $this->linkToRoute('admin-blog-list');
                header("Location: $link ");
            }
        }

        if ($this->isPost()) {
            try {
                foreach ($this->getPost() as $postKey => $postValue) {
                    $this->$postKey = $postValue;
                }

                $this->model->setTitle($this->getPost('title'));
                $this->model->setSeodescription($this->getPost('seodescription'));
                $this->model->setPreview($this->getPost('preview'));
                $this->model->setBody($this->getPost('body'));
                $this->model->setPublishDate($this->getPost('publishdate'));
                $this->model->setTags($this->getPost('tags'));
                $this->model->setUrl($this->getPost('url'));
                $this->model->setStatus($this->getPost('status'));

                $tags = new TagsModel();
                $tags->learnTags(explode(',', $this->getPost('tags')));

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
}
