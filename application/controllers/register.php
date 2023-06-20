<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$allowedOrigin = 'http://localhost:5173';
header('Access-Control-Allow-Origin: ' . $allowedOrigin);
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: set-cookie, session_id, Content-Type, Content-Length, Accept-Encoding");
header('Access-Control-Allow-Credentials: true'); 

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: set-cookie, session_id, Content-Type, Content-Length, Accept-Encoding');
    header('Access-Control-Allow-Credentials: true'); 
    exit;
}


class Register extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model("login_model");
        $this->load->library('ion_auth');
        $this->load->library('session');
        $this->load->helper('cookie');
        // $this->load->library('cors');
        // $this->cors->handle();
    }

    public function create() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true);
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));
       
        switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                    log_message('error',' - No errors');
            break;
            case JSON_ERROR_DEPTH:
                    log_message('error',' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                    log_message('error',  ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                    log_message('error', ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                    log_message('error', ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                    log_message('error', ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                    log_message('error', ' - Unknown error');
            break;
        
        }

        $first_name = ucfirst(strtolower(trim($object["first_name"])));
        $last_name = ucfirst(strtolower(trim($object["last_name"])));
        $email = strtolower(trim($object["email"]));
        $password = trim($object["password"]);
        $password_confirm = trim($object["password_confirm"]);
        // $company = ucfirst(strtolower($object["company"]));
        // $dob = $object["dob"];

        $data = array('status' => 404,
					'message' => 'Kindly ensure that all fields are properly filled'
				);

        if($first_name !== '' && $last_name !== '' && $email !== '' && $password !== '' && $password_confirm !== '') {
            // $diff = abs(strtotime(date('Y-m-d')) - strtotime($dob));
            // $age = (int)floor($diff / (365*60*60*24));

            // if($age > 18) {
                $checkEmail = $this->login_model->checkIfEmailExists($email);

                if($checkEmail) {
                    $data['status'] = 407;
                    $data['message'] = 'user with email already exists'; 
                } 
                else {
                    if ($password === $password_confirm) {
                        $username = $first_name;
                        $email = $email;
                        $password = $password;
                        // $group_ids = [1, 2];
                        $createdBy = $first_name . ' ' . $last_name;
                        $dateCreated = date('Y-m-d H:i:s');
                        // $hashEmail = hash('sha256', $email);
                        $identity =  $first_name;

                        $additional_data = array(
                            'first_name' => $first_name,
                            'last_name' => $last_name,
                            // 'company' => $company,
                            'dateCreated' => $dateCreated,
                            'createdBy' => $createdBy
                        );

                        $register = $this->ion_auth->register($identity, $password, $email, $additional_data);
                        if ($register) {
                            $data['status'] = 200;
                            $data['message'] = 'Registration successful. Kindly log into your email to activate your account';
                        }
                        else {
                            $data['status'] = 402;
                            $data['message'] = 'User added, Please go to email and activate account to be able to login';
                        }
                    } else {
                        $data['status'] = 401;
                        $data['message'] = 'Password mismatch';
                    }
                    
                }  
            // } else {
            //     $data['status'] = 406;
            //     $data['message'] = 'invalid email';
            // }
        } 

        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
    }

    public function login() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true); 
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));
       
        switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                    log_message('error',' - No errors');
            break;
            case JSON_ERROR_DEPTH:
                    log_message('error',' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                    log_message('error',  ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                    log_message('error', ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                    log_message('error', ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                    log_message('error', ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                    log_message('error', ' - Unknown error');
            break;
        
        }

        $identity = strtolower(trim($object["email"]));
        $password = trim($object["password"]);
        $remember = (bool) strtolower(trim($object["remember"]));

        $data = array('status' => 404,
                    'message' => 'Kindly provide your email and password',
                    'userId' => 0
				);

        if ($identity !== '' && $password !== '' && $remember !== '') {
            if (filter_var($identity, FILTER_VALIDATE_EMAIL)) {
                if ($this->ion_auth->login($identity, $password, $remember)) {
                    log_message('error', 'i just logged in from frontend and it was login succesful');

                    if ($this->ion_auth->is_admin()) {
                        $data['admin'] = true;
                    }

                    $id=$this->ion_auth->get_user_id();
                    $sess = $this->session->session_id;

                    $cookie = array(
                        'name'   => 'token',
                        'value'  => $sess,
                        'expire' => 86400,
                        'domain' => 'localhost',
                        'path'   => '/',
                        'samesite' => 'Strict',
                        'httponly' => true,
                    );

                    
                    $this->input->set_cookie($cookie);
                    header('Content-Type: text/plain');

                    if (empty($id)) {
                        $data['status'] = 407;
                        $data['message'] = 'Account deactivated. Contact admin'. ' '.$this->session->userdata('active');
                        $data['userId'] = 0;
                        
                    }
                    else {
                        $userId = $this->login_model->getUserId($identity);
                        if ($userId) {
                            $data['status'] = 200;
                            $data['message'] = 'Login Successful';
                            $data['userId'] = $userId;
                            $data['sess'] = $sess;
                        }
                        else {
                            $data['status'] = 201;
                            $data['message'] = 'An error occured. Please try again';
                            $data['userId'] = 0;
                        }
                    }
                }
                else {
                    $data['status'] = 406;
                    $data['message'] = 'Login Failed';
                    $data['userId'] = 0;
                }
            }
            else {
                $data['status'] = 405;
                $data['message'] = 'Invalid email';
                $data['userId'] = 0;
            }
        }

        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
    }

    public function logout() {
        // $body = file_get_contents("php://input");
        // $object = json_decode($body, true);
        // log_message('error', "logging object create");
        // log_message('error', var_export($object,true));
        // log_message('error', var_export($body,true));

        // if (isset($_COOKIE['token'])) {
        //     $this->input->delete_cookie('token');
        // }
        $logout = $this->ion_auth->logout();
        $cookie = array(
            'name'   => 'token',
            'value'  => '',
            'expire' => '',
            'domain' => 'localhost',
            'path'   => '/',
            'samesite' => 'Strict',
            'httponly' => true,
        );
        $this->input->set_cookie($cookie);
        

        $data = array();

        if ($logout) {
            $data['status'] = 200;
            $data['message'] = 'Logout successful';
        }

        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
    }

    public function change_password() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true);
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));
           
        switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                    log_message('error',' - No errors');
            break;
            case JSON_ERROR_DEPTH:
                    log_message('error',' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                    log_message('error',  ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                    log_message('error', ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                    log_message('error', ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                    log_message('error', ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                    log_message('error', ' - Unknown error');
            break;
        
        }
    
    
        // $email = strtolower(trim($object['identity']));
        // $identity_column = $this->config->item('identity', 'ion_auth');
        // $identity = $this->ion_auth->where($identity_column, $email)->users()->row();
        $identity = $this->session->userdata('identity');
        $oldPassword = trim($object['old_password']);
        $newPassword = trim($object['new_password']);
        $newPassword_confirm = trim($object['new_password_confirm']);
    
        $data = array();

        if (!$this->ion_auth->logged_in())
		{
			redirect('register/login', 'refresh');
		}
        $user = $this->ion_auth->user()->row();
    
        if ($oldPassword !== '' && $newPassword !== '' && $newPassword_confirm !== '') {
            if ($oldPassword !== $newPassword) {
                if ($newPassword === $newPassword_confirm) {
                    $change = $this->ion_auth->change_password($identity, $oldPassword, $newPassword);
                   
				
                    if ($change) {
                        $data['status'] = 200;
                        $data['message'] =  $this->session->flashdata('message');
                        $this->ion_auth->logout();
                    } else {
                        $data['status'] = 408;
                        $data['message'] = $this->session->flashdata('message');;
				        redirect('register/change_password', 'refresh');
                    }
    
                } else {
                    $data['status'] = 401;
                    $data['message'] = 'Password mismatch';
                }
    
            } else {
                $data['status'] = 409;
                $data['message'] = 'Old and New Password match';
            }
        }
    
        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
    
    }
    
    public function forgot_password() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true);
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));
           
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
            log_message('error',' - No errors');
            break;
            case JSON_ERROR_DEPTH:
                log_message('error',' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                log_message('error',  ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                log_message('error', ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                log_message('error', ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                log_message('error', ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                log_message('error', ' - Unknown error');
            break;
        
        }

        $data = array();
    
        $identity_column = $this->config->item('identity', 'ion_auth');
        $email = strtolower(trim($object['email']));
        $identity = $this->ion_auth->where($identity_column, $email)->users()->row();
    
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            
            if (empty($identity))
            {
                
                $data['status'] = 400;
                $data['message'] = 'Sorry, user not found';
                // echo json_encode($data);
            }
    
            $forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});
    
            if ($forgotten)
            {
                $data['status'] = 200;
                $data['message'] = 'Check your email. The link to reset your password has been sent';
                // echo json_encode($data);
            }
            else
            {
                $data['status'] = 500;
                $data['message'] = 'Sorry, there was an error. Please try again';
                // echo json_encode($data);
            }
        }
    
        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
    }
    
    public function delete_user() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true);
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));

        $data = array();
    
        $id = (int) trim($object['id']);
        $delete = $this->ion_auth->delete_user($id);
    
        if ($delete) 
        {
            $data['status'] = 200;
            $data['message'] = 'user account has been deleted successfully';
        }

        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
    
    }
    
    public function edit_user() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true);
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));
           
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
            log_message('error',' - No errors');
            break;
            case JSON_ERROR_DEPTH:
                log_message('error',' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                log_message('error',  ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                log_message('error', ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                log_message('error', ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                log_message('error', ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                log_message('error', ' - Unknown error');
            break;
        
        }

        $user = $this->ion_auth->user($id)->row();
        $first_name = strtolower(trim($object['first_name']));
        $last_name = strtolower(trim($object['last_name']));

        $additionalData = [
            'first_name' => $first_name,
            'last_name' => $last_name,
        ];

        $data = array();

        if ($user !== '' && $additionalData !== '') {
            if ($this->ion_auth->update($user->id, $additionalData)) {
                    $data['status'] = 200;
                    $data['message'] = 'user account has been updated successfully';;
                }
				else {  
                    $data['status'] = 400;
                    $data['message'] = 'user account has not been updated';
				}
        }

        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
    
    }

    // public function AuthCheck() {
    //     $data = array();
        
    //     if ($this->ion_auth->logged_in()) {
    //         $data['status'] = true;
    //         $data['user'] = $this->login_model->getUserDetailsById($this->ion_auth->get_user_id());
    //         $status_code = 200;
    //         http_response_code($status_code);
    //     } else {
    //         $data['status'] = false;
    //         $status_code = 401;
    //         http_response_code($status_code);
    //     }
        
    //     log_message("error", $data);
        
    //     echo json_encode($data);
        
        
    //     // Return the response 
    //      // $cookie_name = 'sess_id';
    //     // $sessionId = $this->input->cookie($cookie_name, TRUE);
    //     // log_message('error', 'omor' . $sessionId);
        
    //     // $data = array();

    //     // if (empty($sessionId) || $sessionId === null) {
    //     //     $status_code = 401;
    //     //     http_response_code($status_code);
    //     // }
    //     // $status_code = 201;
    //     // http_response_code($status_code);
    //     // $data['status'] = true;
    //     // $data['user'] = $this->login_model->getUserDetailsById($this->ion_auth->get_user_id());     
    // }

    public function isLoggedIn()
    {
        $loggedIn = $this->ion_auth->logged_in();
        $userData = null;

        log_message('error', "check if  i am logged in" . $loggedIn);

        if ($loggedIn) {
            $userData = $this->ion_auth->user()->row();
            if ($this->ion_auth->is_admin()) {
                $admin = true;
            }
        }


        $response = array(
            'loggedIn' => $loggedIn,
            'user' => $userData,
            'admin' => $admin
        );

        echo json_encode($response);
    }

    public function cart() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true);
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));
           
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
            log_message('error',' - No errors');
            break;
            case JSON_ERROR_DEPTH:
                log_message('error',' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                log_message('error',  ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                log_message('error', ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                log_message('error', ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                log_message('error', ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                log_message('error', ' - Unknown error');
            break;
        
        }
        
        $quantity = $object['quantity'];
        $product_id = $object['product_id'];
        $user_id = $this->ion_auth->get_user_id();

        $cart_item = '';
        $data = array();

        if ($this->ion_auth->logged_in()) {
            log_message('error', 'user is logged in');
            $cart_item = $this->login_model->addToCart($user_id, $product_id, $quantity);
            $status_code = 200;
            http_response_code($status_code);
            $data['message'] = 'user is logged in, item added to database';
        } else {
            log_message('error', 'user is not logged in');
            $status_code = 302;
            http_response_code($status_code);
            $data['message'] = 'user not logged in';
        }

        

        if ($cart_item) {
            $data['message'] = 'cart updated in db';
        }

        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
        
    }

    public function getCart() {
        if ($this->ion_auth->logged_in()) {
            log_message('error', 'logged'); 
            $uid = $this->ion_auth->get_user_id();
            $cart = $this->login_model->getFromCart($uid);

            echo json_encode($cart);
        }
    }

    public function delete() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true);
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));
           
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
            log_message('error',' - No errors');
            break;
            case JSON_ERROR_DEPTH:
                log_message('error',' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                log_message('error',  ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                log_message('error', ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                log_message('error', ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                log_message('error', ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                log_message('error', ' - Unknown error');
            break;
        
        }

        $pid = $object;
        log_message('error', 'obj' . var_export($object));
        log_message('error', 'pid' . $pid);
        
        if ($this->ion_auth->logged_in()) {
            log_message('error', "deleted in database");
            $del = $this->login_model->deleteFromCart($pid);
            $data['message'] = 'item has been deleted from db';
        } else {
            log_message('error', "not logged in, not deleted in database");
            $data['message'] = 'user is not logged in, can\'t access database';
        }

        $data = array();


        if ($del) {
            $data['message'] = 'item has been deleted from db';
        }

        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);

    }

    public function address() {
        $body = file_get_contents("php://input");
        $object = json_decode($body, true);
        log_message('error', "logging object create");
        log_message('error', var_export($object,true));
        log_message('error', var_export($body,true));
           
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
            log_message('error',' - No errors');
            break;
            case JSON_ERROR_DEPTH:
                log_message('error',' - Maximum stack depth exceeded');
            break;
            case JSON_ERROR_STATE_MISMATCH:
                log_message('error',  ' - Underflow or the modes mismatch');
            break;
            case JSON_ERROR_CTRL_CHAR:
                log_message('error', ' - Unexpected control character found');
            break;
            case JSON_ERROR_SYNTAX:
                log_message('error', ' - Syntax error, malformed JSON');
            break;
            case JSON_ERROR_UTF8:
                log_message('error', ' - Malformed UTF-8 characters, possibly incorrectly encoded');
            break;
            default:
                log_message('error', ' - Unknown error');
            break;
        
        }

        $street_number = $object['street_number'];
        $address_line_1 = $object['address_line_1'];
        $address_line_2 = $object['address_line_2'];
        $country = $object['country'];
        $phone = $object['phone'];
        $region = $object['region'];
        $city = $object['city'];
        $postal_code = $object['postalcode'];
        $is_default = $object['isdefault'];
        $uid = $this->ion_auth->get_user_id();

        if($this->ion_auth->logged_in()) {
            log_message('error', 'user is logger in.. add address');
            $address = $this->login_model->insertAddress($uid, $street_number, $address_line_1, $address_line_2, $country, $region, $city, $postal_code, $is_default);
            $phone = $this->login_model->insertPhoneNumber($phone, $uid);
        }
        
        if ($address) {
            log_message('error', "address has been added in database");
            $data['message'] = 'adress has been added';
        } else {
            log_message('error', "address has not been added in database");
            $data['message'] = 'adress has not been added';
        }

        if ($phone) {
            log_message('error', "address has been added in database");
            $data['message'] = 'adress has been added';
        } else {
            log_message('error', "address has not been added in database");
            $data['message'] = 'adress has not been added';
        }

        log_message('error', 'Before Sending out');
        log_message('error', var_export($data,true));
        header('Content-type: application/json');
        echo json_encode($data);
    }

    public function getUserName() {
        $id = $this->ion_auth->get_user_id();
        $user = $this->login_model->getUsername($id);
        $data = array();

        if ($user) {
            $data['user'] = $user;
        }

        header('Content-type: application/json');
        echo json_encode($data);

    }

    public function getCountry() {
        $countries = $this->login_model->getCountries();

        echo json_encode($countries);
    }
}






