<?php 
/* 

$allowedOrigin = 'http://localhost:5173';
header('Access-Control-Allow-Origin: ' . $allowedOrigin);
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
header('Access-Control-Allow-Credentials: true'); // Add this line

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: ' . $allowedOrigin);
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding');
    header('Access-Control-Allow-Credentials: true'); // Add this line
    exit;
}


class Initialize extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('login_model');
        $this->load->library('ion_auth');
    }

    public function AuthCheck() {
        $data = array();
        $log = $this->ion_auth->logged_in();
        log_message('error', $log);

        if ($log)
		{   
			$data['status'] = true;
            $data['user'] = $this->login_model->getUserDetailsById($this->ion_auth->get_user_id());
		} else {
            $data['status'] = false;
        }

        echo json_encode($data);
    }
}
*/


 // $this->load->library('cors');
        // $this->cors->handle();

        // $body = file_get_contents("php://input");
        // $object = json_decode($body, true);
        // $token = ($object['session']);



