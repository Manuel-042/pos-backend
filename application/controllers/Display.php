<?php 

class Display extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model("Display_model");
        Header('Access-Control-Allow-Origin: http://localhost:5173'); //for allow any domain, insecure
        Header('Access-Control-Allow-Headers: *'); //for allow any headers, insecure
        Header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE'); //method allowed
    }

    public function products() {
        $product = $this->Display_model->getProducts();
        $data = json_encode($product);
        echo $data;
        
    }
}