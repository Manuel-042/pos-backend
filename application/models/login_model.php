<?php 

class Login_model extends CI_Model {
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function checkIfEmailExists($email) {
        $stmt = $this->db->get_where('users', array("email =" => $email));
        return $stmt->row();

        #SELECT * FROM users WHERE email = ":$emal";
    
    }

    public function getUserId($identity) {
        $this->db->select('*');
        $query = $this->db->get_where('users', array("email =" => $identity));
        return $query->row();

        #SELECT id FROM users WHERE email = ':$identity';
    }

    public function getUserDetailsById($id) {
        $this->db->select('*');
        $query = $this->db->get_where('users', array("id =" => $id));
        return $query->row();

        #SELECT id FROM users WHERE email = ':$id';
    }

    public function addToCart($uid, $pid, $quantity) {
        $selectQuery = 'SELECT * FROM shopping_cart WHERE product_id = ? AND user_id = ?';
        $qty2 = $this->db->query($selectQuery, array($pid, $uid));
    
        if ($qty2->num_rows() > 0) {
            log_message('error', 'product already exists');
            $updateQuery = 'UPDATE shopping_cart SET quantity = quantity + ? WHERE product_id = ? AND user_id = ?';
            $qty4 = $this->db->query($updateQuery, array($quantity, $pid, $uid));
        } else {
            log_message('error', 'product doesn\'t exists');
            $insertQuery = 'INSERT INTO shopping_cart (user_id, product_id, quantity) VALUES (?, ?, ?)';
            $query = $this->db->query($insertQuery, array($uid, $pid, $quantity));
        }
        
        
        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }

    }

    public function getFromCart($uid) {
        $sql = 'SELECT t1.product_id, t1.quantity, t2.name, t2.price, CONCAT("http://localhost/CI/posimages/", t2.image) AS image FROM shopping_cart AS t1 INNER JOIN product AS t2 ON t1.product_id = t2.id WHERE t1.user_id = ?';
        $query = $this->db->query($sql, array($uid));
        return $query->result();


    

        //SELECT everything FROM shopping_cart WHERE id = $uid
        //Result: {product_id: 4, quantity: 3}
        //2 options
        //1. loop through products array and filter array by res.data.product_id and display in cart. fill in quantity with res.data.quantity
        //2. join the product table where id == product_id, and send response containing image, name, price, then fill in quantity with res.data.quantity


    }

    public function deleteFromCart($pid) {
        $sql = 'DELETE FROM shopping_cart WHERE product_id = ?';
        $query = $this->db->query($sql, array($pid));

        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insertPhoneNumber($phone, $uid) {
        // $sql = 'UPDATE users SET phone = ? WHERE id = ?';
        // $query = $this->db->query($sql, $phone, $uid);

        $data = array(
            'phone' => $phone
        );

        $this->db->where('id', $uid)->update('users', $data);


        if ($this->db->affected_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function insertAddress($uid, $street_number, $address_line_1, $address_line_2, $country, $region, $city, $postal_code, $is_default) {
        $sql = $this->db->insert('address', array(
            'user_id' => $uid,
            'street_number' => $street_number, 
            'address_line_1' => $address_line_1, 
            'address_line_2' => $address_line_2, 
            'region' => $region, 
            'city' => $city, 
            'postal_code' => $postal_code,
            'country_id' => $this->db->select('country_id')->from('country')->where('country_name', $country)->get()->row()->country_id,
            'is_default' => $is_default,
        ));
    }

    public function getUsername($id) {
        $query = $this->db->get_where('users', array("id =" => $id));
        return $query->row();
    }

    public function getCountries() {
        $query = $this->db->select('country_name')->get('country');

        $countries = array();
        foreach ($query->result() as $row) {
            $countries[] = $row->country_name;
        }

        return $countries;
    }




}