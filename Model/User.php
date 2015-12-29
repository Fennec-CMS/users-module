<?php
/**
 ************************************************************************
 * @copyright 2015 David Lima
 * @license Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0) 
 ************************************************************************
 */
namespace Fennec\Modules\Users\Model;

use \Fennec\Model\Base;

/**
 * User model
 *
 * @author David Lima
 * @version 1.0
 */
class User extends Base
{
    use \Fennec\Library\Security;

    /**
     * Table to save data
     *
     * @var string
     */
    public static $table = "users";

    /**
     * Real name
     *
     * @var string
     */
    public $name;

    /**
     * Usernname
     *
     * @var string
     */
    public $username;

    /**
     * Email
     *
     * @var string
     */
    public $email;

    /**
     * User password
     *
     * @var string
     */
    public $password;

    /**
     * User status
     *
     * @var int
     */
    public $status;

    /**
     * User id
     *
     * @var int
     */
    public $id;

    /**
     * User register datetime
     *
     * @var string
     */
    public $timestamp;

    /**
     * Register a user
     *
     * @return PDOStatement
     */
    public function create()
    {
        $data = $this->prepare();

        if (isset($data['valid']) && ! $data['valid']){
            return $data;
        } else {
            try {
                if ($this->id) {
                    $post = $this->getByColumn('id', $this->id)[0];
                    $query = $this->update(self::$table)
                        ->set($data)
                        ->where("id = '{$this->id}'")
                        ->execute();
                } else {
                    $query = $this->insert($data)
                        ->into(self::$table)
                        ->execute();
                    
                    $this->id = $query;
                }

                return array(
                    'result' => (isset($post) ? 'User updated!' : 'User created!')
                );
            } catch (\Exception $e) {
                return array(
                    'result' => 'Failed to register user!',
                    'errors' => array($e->getMessage())
                );
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see \Fennec\Model\Base::getAll()
     */
    public function getAll()
    {
        return $this->select("*")
            ->from(self::$table)
            ->order('timestamp', 'DESC')
            ->execute();
    }

    /**
     * Prepare data to create administrator
     *
     * @return multitype:string |multitype:\Fennec\Model\string \Fennec\Model\integer
     */
    private function prepare()
    {
        $errors = $this->validate();
        if (! $errors['valid']) {
            return $errors;
        }
        
        if ($this->id && ! $this->password) {
            $user = $this->getByColumn('id', $this->id, 1);
            $this->password = $user->password;
        } else {
            $this->password = self::hash($this->password);
        }
        
        $this->name = filter_var($this->name, \FILTER_SANITIZE_STRING);
        $this->username = filter_var($this->username, \FILTER_SANITIZE_STRING);
        $this->email = filter_var($this->email, \FILTER_SANITIZE_STRING);
        $this->status = intval($this->status);
        $this->timestamp = (empty($this->timestamp) ? date("Y-m-d H:i:s") : $this->timestamp);

        return array(
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'status' => $this->status,
            'timestamp' => $this->timestamp
        );
    }
    
    /**
     * Authenticate an user
     *
     * @return boolean
     */
    public function authenticate()
    {
        $this->username = filter_var($this->username, \FILTER_SANITIZE_STRING);
        $userExists = $this->select('*')
        ->from(self::$table)
        ->where("username = '{$this->username}'")
        ->limit(1)
        ->execute();
        $userData = $userExists->fetch();
    
        if ($userData) {
            if ($this->verify($this->password, $userData->getPassword())) {
                $_SESSION['fennecUser'] = $userData;
                return true;
            }
        }
    
        return false;
    }

    /**
     * Validate post data
     *
     * @return multitype:string
     */
    private function validate()
    {
        $validation = array(
            'valid' => true,
            'errors' => array()
        );

        if (! $this->name) {
            $validation['valid'] = false;
            $validation['errors']['name'] = "Name is a required field";
        }

        if (! $this->username) {
            $validation['valid'] = false;
            $validation['errors']['username'] = "Username is a required field";
        }
        
        if (! $this->email) {
            $validation['valid'] = false;
            $validation['errors']['email'] = "Email is a required field";
        }

        return $validation;
    }
}
