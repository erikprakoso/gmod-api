<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Promocode extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //load model
        $this->load->model('M_promocode');

        $this->load->library('secure');
        $this->load->library('aes256');

        //load library form validasi
        $this->load->library('form_validation');
    }

    /**
     * Simpan Data
     */
    public function simpan()
    {
        $request_data = json_decode(file_get_contents('php://input'), true);

        if ($request_data != NULL) {

            $data = array(
                'code'      => $request_data['code']
            );

            $simpan = $this->M_promocode->insert($data);

            if ($simpan) {

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'success' => true,
                        'message' => 'Data Berhasil Disimpan!'
                    )
                );
            } else {

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'success' => false,
                        'message' => 'Data Gagal Disimpan!'
                    )
                );
            }
        } else {

            header('Content-Type: application/json');
            echo json_encode(
                array(
                    'success'    => false,
                    'message'    => validation_errors()
                )
            );
        }
    }

    public function encrypt_request()
    {
        $output = $this->aes256->encrypt($this->input->post("data"));
        header('Content-Type: application/json');
        echo $output;
    }

    public function decrypt_request()
    {
        $output = $this->aes256->decrypt($this->input->post("data"));
        header('Content-Type: application/json');
        echo $output;
    }

    public function v1()
    {
        $this->form_validation->set_rules('data', 'Data', 'required');

        if ($this->form_validation->run() == TRUE) {
            $decrypt_data = json_decode($this->aes256->decrypt($this->input->post("data")));

            $result = $this->M_promocode->get_by_code($decrypt_data->code);
            $row = $result['row'];
            $totalRows = $result['totalRows'];
            if ($row) {
                if ($row->count > $row->count_max || $row->count == $row->count_max ) {
                    // header('Content-Type: application/json');
                    echo json_encode(
                        array(
                            'success' => false,
                            'message' => 'Code has reached the limit',
                            'data' => null
                        )
                    );
                } else {
                    $data = array(
                        'count' => intval($row->count) + 1
                    );
                    $this->M_promocode->update_by_id($row->id, $data);
                    $data = array(
                        "data" => array(
                            "present" => $row->data
                        ),
                        "result" => intval($totalRows)
                    );

                    $jsonString = json_encode($data);
                    $output = $this->aes256->encrypt($jsonString);
                    // header('Content-Type: application/json');
                    echo $output;
                }
            } else {

                // header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'success' => false,
                        'message' => 'Data not found',
                        'data' => null
                    )
                );
            }
        } else {

            // header('Content-Type: application/json');
            echo json_encode(
                array(
                    'success'    => false,
                    'message'    => validation_errors(),
                    'data'       => null
                )
            );
        }
    }
}
