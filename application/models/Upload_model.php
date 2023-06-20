<?php 

class Upload_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insertCountry($countryData) {
        $uniqueCountries = array_unique($countryData);

        foreach ($uniqueCountries as $country) {
            $this->db->insert('country', ['country_name' => $country]); 
        }

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
        
    }

    public function insertCategory($categoryData) {
        $uniqueCategories = array_unique($categoryData);

        foreach ($uniqueCategories as $category) {
            $this->db->insert('product_category', ['category_name' => $category]); 
        }

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
        
    }

    public function insertProduct($products) {
        foreach ($products as $product) {
            $this->db->insert('product', array(
                'name' => $product['name'],
                'company' => $product['company'],
                'image' => $product['image'],
                'price' => $product['price'],
                'size' => $product['size'],
                'brand_family' => $product['brand_family'],
                'brand_type' => $product['brand_type'],
                'updated_date' => $product['date'][0],
                'updated_time' => $product['date'][1],
                'categories_id' => $this->db->select('id')->from('product_category')->where('category_name', $product['category'])->get()->row()->id
            ));
        }

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
}