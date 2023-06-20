<?php 

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

class store extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->library('ion_auth');
    }

    public function cart() {
        $body = file_get_contents("php://input");
        log_message('error', $body);
        $object = json_decode($body, true);
        log_message('error', 'here');
        log_message('error', $object);
        
    
        // $name = $object['name'];
        // $price = $object['price'];
        $quantity = $object['quantity'];
        $product_id = $object['product_id'];
        $user_id = $this->ion_auth->get_user_id();

        $cart_item = $this->login_model->addToCart($user_id, $product_id, $quantity);

        $data = array();

        if ($cart_item) {
            $data['message'] = 'cart updated in db';
        }

        echo json_encode($data);
        
    }

    public function demo() {
        $body = file_get_contents("php://input");

        log_message('error', $body);

        $data = array();

        $data['message'] = 'its coming';

        echo json_encode($data);
    }
}