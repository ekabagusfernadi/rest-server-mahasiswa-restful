<?php

// koneksi ke library
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Mahasiswa extends REST_Controller {   // CI_Controller ganti ke REST_Controller

    public function __construct()
    {
        parent::__construct(); // karena modul loadnya ada di parent
        $this->load->model("Mahasiswa_model", "mahasiswa");   // load dulu modul modelnya, paramater2 = nama alias Model_mahasiswa

        $this->methods['index_get']['limit'] = 2000;    // aturan limit key, per request method dan batasnya perjam(misal perjam hanya 2 kali request)
        $this->methods['index_delete']['limit'] = 200;
    }

    public function index_get()
    {   // methodnya ditambahi _get = request mehtod get, request method yang dikirim dari postman kalau GET ditangkap _get()

        $id = $this->get("id"); // ngecek apakah di http request method GET ada parameter id atau tidak, jika ada masukkan ke $id
        if( $id === null ) {
            $mahasiswa = $this->mahasiswa->getMahasiswa();
        } else {
            $mahasiswa = $this->mahasiswa->getMahasiswa($id);
        }

        
        if( $mahasiswa ) {  // jika $mahasiswa ada isinya maka ubah array returnnya ke json, pakai cara dari library REST_Server
            $this->response([
                'status' => true,   // status terserah bisa diubah2, status ini nanti akan dikirim bersama dengan return json
                // 'message' => 'No users were found'   // bisa tidak pakai pesan
                'data' => $mahasiswa    // tampilkan data mahasiswa
            ], REST_Controller::HTTP_OK);   // kalau data berhasil ditampilkan ada http_ok, kembalian status_code nya 200
        } else {
            $this->response([
                'status' => false,   // status terserah bisa diubah2, status ini nanti akan dikirim bersama dengan return json
                'message' => 'Id mahasiswa tidak ada'
                // 'data' => $mahasiswa    // data tidak ada
            ], REST_Controller::HTTP_NOT_FOUND);    // data tidak ada, status_code nya 404
        }

    }

    // http request delete
    public function index_delete()  // tetap controller mahasiswa method index tapi dengan request menthod delete(endpoints sama beda request method)
    {
        $id = $this->delete("id");  // dari request method delete paramter id

        if( $id === null ) {
            $this->response([
                'status' => false,   // status terserah bisa diubah2, status ini nanti akan dikirim bersama dengan return json
                'message' => 'provide an id!'
                // 'data' => $mahasiswa    // data tidak ada
            ], REST_Controller::HTTP_BAD_REQUEST);    // status_code untuk gagal hapus, status code yang lain bisa dilihat di Example.php
        } else {
            if( $this->mahasiswa->hapusMahasiswa($id) > 0 ) {   // > 0 artinya ada mahasiswa yang terhapus
                $this->response([
                    'status' => true,
                    'id' => $id,
                    'message' => 'data berhasil dihapus.'
                ], REST_Controller::HTTP_OK);   // hati2 HTTP_NO_CONTENT tidak menampilkan pesan response
            } else {
                $this->response([
                    'status' => false,   // status terserah bisa diubah2, status ini nanti akan dikirim bersama dengan return json
                    'message' => 'id not found!'
                    // 'data' => $mahasiswa    // data tidak ada
                ], REST_Controller::HTTP_BAD_REQUEST);
            }
        }
    }

    public function index_post()
    {
        $data = [   // data dari http request method post, jadikan array associative
            "nrp" => $this->post("nrp"),    // data diambil per key
            "nama" => $this->post("nama"),
            "email" => $this->post("email"),
            "jurusan" => $this->post("jurusan")
        ];

        if( $this->mahasiswa->tambahMahasiswa($data) > 0 ) {
            $this->response([
                'status' => true, 
                'message' => 'data berhasil ditambahkan'
            ], REST_Controller::HTTP_CREATED);
        } else {
            $this->response([
                'status' => false,
                'message' => 'data gagal ditambahkan!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }

    public function index_put()
    {
        $id = $this->put("id"); // dibedakan agar id bisa masuk ke wherenya
        $data = [
            "nrp" => $this->put("nrp"),    // data diambil per key
            "nama" => $this->put("nama"),
            "email" => $this->put("email"),
            "jurusan" => $this->put("jurusan")
        ];

        if( $this->mahasiswa->ubahMahasiswa($data, $id) > 0 ) {
            $this->response([
                'status' => true, 
                'message' => 'data berhasil diubah'
            ], REST_Controller::HTTP_OK);
        } else {
            $this->response([
                'status' => false,
                'message' => 'data gagal diubah!'
            ], REST_Controller::HTTP_BAD_REQUEST);
        }
    }
}

?>