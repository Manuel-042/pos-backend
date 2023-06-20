<?php 

$config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_port' => 465,
            'smtp_user' => 'xxx',
            'smtp_pass' => 'xxx',
            'mailtype' => 'html'
            ];
$data = array(
    'identity'=>$forgotten['identity'],
    'forgotten_password_code' => $forgotten['forgotten_password_code'],
);
$this->load->library('email');
$this->email->initialize($config);
$this->load->helpers('url');
$this->email->set_newline("\r\n");

$this->email->from('xxx');
$this->email->to("xxx");
$this->email->subject("forgot password");
$body = $this->load->view('auth/email/forgot_password.tpl.php',$data,TRUE);
$this->email->message($body);

if ($this->email->send()) {

    $this->session->set_flashdata('success','Email Send sucessfully');
    return redirect('auth/login');
} 
else {
    echo "Email not send .....";
    show_error($this->email->print_debugger());
}

/* =============================================
EMAIL FILE
==============================================*/

$this->load->library('email');

$this->email->from('your@example.com', 'Your Name');
$this->email->to('someone@example.com');
$this->email->cc('another@another-example.com');
$this->email->bcc('them@their-example.com');

$this->email->subject('Email Test');
$this->email->message('Testing the email class.');

$this->email->send();