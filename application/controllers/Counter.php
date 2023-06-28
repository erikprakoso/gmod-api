<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Counter extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //load model
        $this->load->model('M_counter');

        $this->load->library('secure');
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

            $simpan = $this->M_counter->insert($data);

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
        $request_data = json_decode(file_get_contents('php://input'), true);
        header('Content-Type: application/json');
        echo json_encode(
            array(
                'success' => true,
                'message' => 'Data Berhasil ditemukan!',
                'data' => $this->secure->encrypt_url(json_encode($request_data))
            )
        );
    }

    public function get_by_code()
    {
        $request_data = json_decode(file_get_contents('php://input'), true);
        
        if ($request_data != NULL) {
            $decrypt_data = json_decode($this->secure->decrypt_url($request_data['data']));

            $row = $this->M_counter->get_by_code($decrypt_data->code);

            if ($row) {
                if ($row->count_max >= 10) {
                    header('Content-Type: application/json');
                    echo json_encode(
                        array(
                            'success' => false,
                            'message' => 'Data lebih dari 10 kali!',
                            'data' => null
                        )
                    );
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(
                        array(
                            'success' => true,
                            'message' => 'Data Berhasil ditemukan!',
                            'data' => $this->secure->encrypt_url(json_encode($row))
                        )
                    );
                }
            } else {

                header('Content-Type: application/json');
                echo json_encode(
                    array(
                        'success' => false,
                        'message' => 'Data Gagal ditemukan!',
                        'data' => $row
                    )
                );
            }
        } else {

            header('Content-Type: application/json');
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
