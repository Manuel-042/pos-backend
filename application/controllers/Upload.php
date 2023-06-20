<?php 

if (file_exists(APPPATH . 'vendor/autoload.php')) {
    require_once(APPPATH . 'vendor/autoload.php');
} elseif (file_exists(APPPATH . '../vendor/autoload.php')) {
    require_once(APPPATH . '../vendor/autoload.php');
}

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Upload extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model("Upload_model");
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function index() {
        $this->load->view("upload");
    }
    
    public function import() {
        if (isset($_POST["importSubmit"])) {
            $fileName = $_FILES['upload_file']['name'];
            $files_ext = pathinfo($fileName, PATHINFO_EXTENSION);
    
            if ($files_ext == "xls") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } else if ($files_ext == "csv") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            }
    
            $spreadsheet = $reader->load($_FILES['upload_file']['tmp_name']);
            $sheetdata = $spreadsheet->getActiveSheet()->toArray();
            // echo  '<pre>';
            // print_r($sheetdata);
            $sheetcount = count($sheetdata);
            // $country = array();
            // $category = array();
            $products = array();

            if ($sheetcount > 1) {
                for ($i = 1; $i < $sheetcount; $i++) {
                    // $thumb = $sheetdata[$i][1];
                    $file = $sheetdata[$i][2];
                    $name = $sheetdata[$i][3];
                    // echo $variant = $sheetdata[$i][3];
                    $brand_family = $sheetdata[$i][4];
                    $pack_price = $sheetdata[$i][5];
                    $category = $sheetdata[$i][6];
                    if (!$category) {
                        $category = 'No Category';
                        // array_push($category, $data);
                    }
                    $pack_size = $sheetdata[$i][7];
                    // echo $key_region = $sheetdata[$i][8];
                    // $country = $sheetdata[$i][9];
                    $company = $sheetdata[$i][10];
                    $brand_type = $sheetdata[$i][11];
                    $dateTime = $sheetdata[$i][12];
                    $last_updated = DateTime::createFromFormat('d/m/Y H:i:s', $dateTime);
                    if ($last_updated !== false) {
                        $last_updated = $last_updated->format('Y-m-d H:i:s');
                    } else {
                        $last_updated = $dateTime; 
                    }

                    #remeber when calling from databse base_url() . 'posimages/' . $file,

                    $products[] = array(
                        'name' => $name,
                        'image' => $file,
                        'price' => $pack_price,
                        'size' => $pack_size,
                        'company' => $company,
                        'category' => $category,
                        'brand_family' => $brand_family,
                        'brand_type' => $brand_type,
                        'date' => explode(" ", $last_updated),
                    );
                }
                // echo '<pre>';
                // print_r($products);
                // echo '</pre>';
            }
            //$categories = $this->Upload_model->insertCategory($category);
            // $insertCountry = $this->Upload_model->insertCountry($country);
            $product = $this->Upload_model->insertProduct($products);

            if ($product) {
                $this->session->set_flashdata("success","database succesfully updated");
                redirect("upload");
            } else {
                $this->session->set_flashdata("error","database did not update");
                redirect("upload");
            }
            
        }
        
    }
}