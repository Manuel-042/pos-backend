<?php
header('Content-Type: image.png');

class Display_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function getProducts() {
        $query = $this->db->get('product');
        $product = array();

        foreach ($query->result() as $row)
        {
            // print_r($row->id);
            $product[] =   (object) array(
                'id' => $row->id,
                'name' => $row->name,
                'price' => $row->price,
                'size' => $row->size,
                'image' => base_url() . "posimages/" . $row->image,
                'company' => $row->company,
                'brand_family' => $row->brand_family,
                'brand_type' => $row->brand_type,
            );
           
        }
        return $product;
        // echo '<pre>';
        // print_r($product);
        // echo '</pre>';
    }
}
