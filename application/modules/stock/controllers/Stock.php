<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Stock extends MX_Controller {

    function __construct() {
        parent::__construct();

        $this->load->model('hospital_model');
        $this->load->model('hospital/package_model');
        $this->load->model('donor/donor_model');
        $this->load->model('pgateway/pgateway_model');
        $this->load->model('sms/sms_model');
        $this->load->model('email/email_model');
        $this->load->model('medicine/medicine_model');
        if (!$this->ion_auth->in_group('superadmin')) {
            redirect('home/permission');
        }
    }

    public function index() {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $data['packages'] = $this->package_model->getPackage();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('hospital', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function addNewView() {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $data['medicines'] = $this->medicine_model->getMedicine();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_new', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function transfer() {
        $post_data = $_POST;
        // echo '<pre>'; print_r($post_data); exit;

        $from_hospital = $this->input->post('from_hospital');
        $to_hospital = $this->input->post('to_hospital');
        $qty = explode(',',$this->input->post('qty'));
        $medicines = $this->input->post('medicines');

        for($i = 0; $i < count($medicines); $i++){
            
            $this->db->where('id', $medicines[$i]);
            $this->db->where('hospital_id', $from_hospital);
            $from_hospital_result = $this->db->get('medicine')->row_array();
            // echo '<pre>'; print_r($from_hospital_result);exit;

            if($from_hospital_result){
                // $this->db->where('id', $medicines[$i]);
                $this->db->where('hospital_id', $to_hospital);
                $this->db->where('name', $from_hospital_result['name']);
                $query = $this->db->get('medicine');
                // echo '<pre>'; print_r($query);//exit;

                if($query->num_rows() > 0){
                    $result = $query->row_array();
                    $data['quantity'] = $result['quantity'] + $qty[$i];
                    // echo '<pre>'; print_r($to_hospital);exit;

                    // $this->db->where('id', $medicines[$i]);
                    $this->db->where('name', $from_hospital_result['name']);
                    $this->db->where('hospital_id', $to_hospital);
                    $this->db->update('medicine', $data);

                    $notifi_data = array( 
                        'medicine_id' => $medicines[$i],
                        'hospital_id' => $to_hospital,
                        'is_view' => 0,
                    );
                    $this->db->insert('medicine_transfer_notification', $notifi_data);
                }else{
                    
                    $data = array(
                        'name' => $from_hospital_result['name'],
                        'category' => $from_hospital_result['category'],
                        'price' => $from_hospital_result['price'],
                        'box' => $from_hospital_result['box'],
                        's_price' => $from_hospital_result['s_price'],
                        'quantity' => $qty[$i],
                        'generic' => $from_hospital_result['generic'],
                        'company' => $from_hospital_result['company'],
                        'effects' => $from_hospital_result['effects'],
                        'e_date' => $from_hospital_result['e_date'],
                        'add_date' => $from_hospital_result['add_date'],
                        'hospital_id' => $to_hospital,
                    );

                    $this->db->insert('medicine', $data);
                    $insert_id = $this->db->insert_id();
                    
                    $notifi_data = array( 
                        'medicine_id' => $insert_id,
                        'hospital_id' => $to_hospital,
                        'is_view' => 0,
                    );
                    $this->db->insert('medicine_transfer_notification', $notifi_data);
                }

                $from_hospital_data['quantity'] = $from_hospital_result['quantity'] - $qty[$i];
                $this->db->where('id', $medicines[$i]);
                $this->db->where('hospital_id', $from_hospital);
                $this->db->update('medicine', $from_hospital_data);

                $notifi_data = array( 
                    'medicine_id' => $medicines[$i],
                    'hospital_id' => $from_hospital,
                    'is_view' => 0,
                );
                $this->db->insert('medicine_transfer_notification', $notifi_data);

            }else{ 
                $this->session->set_flashdata('feedback', 'Does not exist this medicine.');
                redirect('stock/addNewView');
            }
        }

        $this->session->set_flashdata('feedback', 'Updated');
        redirect('stock/addNewView');
    }

    public function addNew() {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $password = $this->input->post('password');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $package = $this->input->post('package');
        $language = $this->input->post('language');


        if (!empty($package)) {
            $module = $this->package_model->getPackageById($package)->module;
            $p_limit = $this->package_model->getPackageById($package)->p_limit;
            $d_limit = $this->package_model->getPackageById($package)->d_limit;
        } else {
            $p_limit = $this->input->post('p_limit');
            $d_limit = $this->input->post('d_limit');
            $module = $this->input->post('module');
            if (!empty($module)) {
                $module = implode(',', $module);
            }
        }

        $language_array = array('english', 'arabic', 'spanish', 'french', 'italian', 'portuguese');

        if (!in_array($language, $language_array)) {
            $language = 'english';
        }

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Name Field
        $this->form_validation->set_rules('name', 'Name', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        // Validating Password Field
        if (empty($id)) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        }
        // Validating Email Field
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[100]|xss_clean');
        // Validating Address Field   
        $this->form_validation->set_rules('address', 'Address', 'trim|required|min_length[5]|max_length[500]|xss_clean');
        // Validating Phone Field           
        $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[5]|max_length[50]|xss_clean');

        // Validating Phone Field           
        $this->form_validation->set_rules('p_limit', 'Patient Limit', 'trim|required|min_length[1]|max_length[100]|xss_clean');

        // Validating Phone Field           
        $this->form_validation->set_rules('language', 'Language', 'trim|required|min_length[1]|max_length[50]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                redirect("hospital/editHospital?id=$id");
            } else {
                $data['packages'] = $this->package_model->getPackage();
                $this->load->view('home/dashboard'); // just the header file
                $this->load->view('add_new', $data);
                $this->load->view('home/footer'); // just the header file
            }
        } else {
            //$error = array('error' => $this->upload->display_errors());
            $data = array();
            $data = array(
                'name' => $name,
                'email' => $email,
                'address' => $address,
                'phone' => $phone,
                'package' => $package,
                'p_limit' => $p_limit,
                'd_limit' => $d_limit,
                'module' => $module
            );

            $username = $this->input->post('name');

            if (empty($id)) {     // Adding New Hospital
                if ($this->ion_auth->email_check($email)) {
                    $this->session->set_flashdata('feedback', 'This Email Address Is Already Registered');
                    redirect('hospital/addNewView');
                } else {
                    $dfg = 11;
                    $this->ion_auth->register($username, $password, $email, $dfg);
                    $ion_user_id = $this->db->get_where('users', array('email' => $email))->row()->id;
                    $this->hospital_model->insertHospital($data);
                    $hospital_user_id = $this->db->get_where('hospital', array('email' => $email))->row()->id;
                    $id_info = array('ion_user_id' => $ion_user_id);
                    $this->hospital_model->updateHospital($hospital_user_id, $id_info);
                    $hospital_settings_data = array();
                    $hospital_settings_data = array('hospital_id' => $hospital_user_id,
                        'title' => $name,
                        'email' => $email,
                        'address' => $address,
                        'phone' => $phone,
                        'language' => $language,
                        'system_vendor' => 'Code Aristos | Hospital management System',
                        'discount' => 'flat',
                        'currency' => '$'
                    );
                    $this->settings_model->insertSettings($hospital_settings_data);
                    $hospital_blood_bank = array();
                    $hospital_blood_bank = array('A+' => '0 Bags', 'A-' => '0 Bags', 'B+' => '0 Bags', 'B-' => '0 Bags', 'AB+' => '0 Bags', 'AB-' => '0 Bags', 'O+' => '0 Bags', 'O-' => '0 Bags');
                    foreach ($hospital_blood_bank as $key => $value) {
                        $data_bb = array('group' => $key, 'status' => $value, 'hospital_id' => $hospital_user_id);
                        $this->donor_model->insertBloodBank($data_bb);
                        $data_bb = NULL;
                    }

                    $data_sms_clickatell = array();
                    $data_sms_clickatell = array(
                        'name' => 'Clickatell',
                        'username' => 'Your ClickAtell Username',
                        'password' => 'Your ClickAtell Password',
                        'api_id' => 'Your ClickAtell Api Id',
                        'user' => $this->ion_auth->get_user_id(),
                        'hospital_id' => $hospital_user_id
                    );

                    $this->sms_model->addSmsSettings($data_sms_clickatell);

                    $data_sms_msg91 = array(
                        'name' => 'MSG91',
                        'username' => 'Your MSG91 Username',
                        'api_id' => 'Your MSG91 API ID',
                        'authkey' => 'Your MSG91 Auth Key',
                        'user' => $this->ion_auth->get_user_id(),
                        'hospital_id' => $hospital_user_id
                    );

                    $this->sms_model->addSmsSettings($data_sms_msg91);



                    $data_pgateway_paypal = array(
                        'name' => 'PayPal', // Sandbox / testing mode option.
                        'APIUsername' => 'PayPal API Username', // PayPal API username of the API caller
                        'APIPassword' => 'PayPal API Password', // PayPal API password of the API caller
                        'APISignature' => 'PayPal API Signature', // PayPal API signature of the API caller
                        'status' => 'test',
                        'hospital_id' => $hospital_user_id
                    );

                    $this->pgateway_model->addPaymentGatewaySettings($data_pgateway_paypal);

                    $data_pgateway_payumoney = array(
                        'name' => 'Pay U Money', // Sandbox / testing mode option.
                        'merchant_key' => 'Merchant key', // PayPal API username of the API caller
                        'salt' => 'Salt', // PayPal API password of the API caller
                        'status' => 'test',
                        'hospital_id' => $hospital_user_id
                    );

                    $this->pgateway_model->addPaymentGatewaySettings($data_pgateway_payumoney);


                    $data_email_settings = array(
                        'admin_email' => 'Admin Email', // Sandbox / testing mode option.
                        'hospital_id' => $hospital_user_id
                    );

                    $this->email_model->addEmailSettings($data_email_settings);

                    $this->session->set_flashdata('feedback', 'New Hospital Created');
                    redirect('hospital');
                }
            } else { // Updating Hospital
                $ion_user_id = $this->db->get_where('hospital', array('id' => $id))->row()->ion_user_id;
                if (empty($password)) {
                    $password = $this->db->get_where('users', array('id' => $ion_user_id))->row()->password;
                } else {
                    $password = $this->ion_auth_model->hash_password($password);
                }
                $this->hospital_model->updateIonUser($username, $email, $password, $ion_user_id);
                $this->hospital_model->updateHospital($id, $data);

                $hospital_settings_data = array();
                $hospital_settings_data = array(
                    'language' => $language
                );
                $this->settings_model->updateHospitalSettings($id, $hospital_settings_data);


                $this->session->set_flashdata('feedback', 'Updated');
                redirect('hospital/editHospital?id=' . $id);
            }
            // Loading View
        }
    }

    function getHospital() {
        $data['hospitals'] = $this->hospital_model->getHospital();
        $this->load->view('hospital', $data);
    }

    function activate() {
        $hospital_id = $this->input->get('hospital_id');
        $data = array('active' => 1);
        $this->hospital_model->activate($hospital_id, $data);
        $this->session->set_flashdata('feedback', 'Activated');
        redirect('hospital');
    }

    function deactivate() {
        $hospital_id = $this->input->get('hospital_id');
        $data = array('active' => 0);
        $this->hospital_model->deactivate($hospital_id, $data);
        $this->session->set_flashdata('feedback', 'Deactivated');
        redirect('hospital');
    }

    function editHospital() {
        $data = array();
        $id = $this->input->get('id');
        $data['packages'] = $this->package_model->getPackage();
        $data['hospital'] = $this->hospital_model->getHospitalById($id);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_new', $data);
        $this->load->view('home/footer'); // just the footer file
    }

    function editHospitalByJason() {
        $id = $this->input->get('id');
        $data['hospital'] = $this->hospital_model->getHospitalById($id);
        $data['settings'] = $this->settings_model->getSettingsByHId($id);
        echo json_encode($data);
    }

    function delete() {
        $data = array();
        $id = $this->input->get('id');
        $user_data = $this->db->get_where('hospital', array('id' => $id))->row();
        $ion_user_id = $user_data->ion_user_id;
        $this->db->where('id', $ion_user_id);
        $this->db->delete('users');
        $this->hospital_model->delete($id);
        redirect('hospital');
    }

}

/* End of file hospital.php */
/* Location: ./application/modules/hospital/controllers/hospital.php */
