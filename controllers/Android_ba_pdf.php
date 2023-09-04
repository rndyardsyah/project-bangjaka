<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by ERGUN.
 * 03/08/2018
 * Controller untuk generate pdf dari android
 */

class Android_ba_pdf extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('pembayaran_model', 'pembayaran');
        $this->load->model('pencairan_model', 'pencairan');
        $this->load->model('penyedia_model', 'penyedia');
        $this->load->model('hasil_pekerjaan_model', 'hasil_pekerjaan');
        $this->load->model('spk_model', 'spk');
        $this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
    }
    
    public function ba($id_pembayaran = '') {
        
        if (!empty($id_pembayaran)) {
            $data_all                   = $this->hasil_pekerjaan->get_by_id_detail($id_pembayaran);
            $content['data_pembayaran'] = $data_all;
            foreach ($data_all as $data) {
                $content['data'] = $data;
                $html1           = $this->load->view('hasil_pekerjaan/template_surat_penyerahan_hasil_pekerjaan', @$content, true);
                $html2           = $this->load->view('hasil_pekerjaan/template_bas', @$content, true);
                $html3           = $this->load->view('hasil_pekerjaan/template_bast', @$content, true);
                
                //create pdf
                $post['paper'] = 'A4';
                // require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
                // $dompdf = new Dompdf\Dompdf();
                require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
                $dompdf = new Dompdf\Dompdf();
                $dompdf->load_html($html1);
                //$dompdf->set_paper('legal', 'portrait');
                if ($post['paper'] == 'f4') {
                    $paper_size = array(
                        0,
                        0,
                        612.00,
                        936.00
                    ); //opick
                    $dompdf->set_paper($paper_size);
                } else {
                    $dompdf->set_paper($post['paper'], 'portrait');
                }
                $dompdf->render();
                $output = $dompdf->output();
                
                $filename1    = 'surat_penyerahan_hasil_pekerjaan_termin_' . $data->termin . '_' . $data->id_hasil_pekerjaan . '.pdf';
                $file_to_save = 'assets/file/surat/' . $filename1;
                file_put_contents($file_to_save, $output);
                unset($dompdf);
                
                $dompdf = new Dompdf\Dompdf();
                // $dompdf = new Dompdf\Dompdf();
                $dompdf->load_html($html2);
                
                if ($post['paper'] == 'f4') {
                    $paper_size = array(
                        0,
                        0,
                        612.00,
                        936.00
                    ); //opick
                    $dompdf->set_paper($paper_size);
                } else {
                    $dompdf->set_paper($post['paper'], 'portrait');
                }
                $dompdf->render();
                $output = $dompdf->output();
                
                $filename2    = 'surat_berita_acara_penerimaan_hasil_pekerjaan_termin_' . $data->termin . '_' . $data->id_hasil_pekerjaan . '.pdf';
                $file_to_save = 'assets/file/surat/' . $filename2;
                file_put_contents($file_to_save, $output);
                unset($dompdf);
                
                $dompdf = new Dompdf\Dompdf();
                // $dompdf = new Dompdf\Dompdf();
                $dompdf->load_html($html3);
                
                if ($post['paper'] == 'f4') {
                    $paper_size = array(
                        0,
                        0,
                        612.00,
                        936.00
                    ); //opick
                    $dompdf->set_paper($paper_size);
                } else {
                    $dompdf->set_paper($post['paper'], 'portrait');
                }
                $dompdf->render();
                $output = $dompdf->output();
                
                $filename3    = 'surat_berita_acara_serah_terima_pekerjaan_termin_' . $data->termin . '_' . $data->id_hasil_pekerjaan . '.pdf';
                $file_to_save = 'assets/file/surat/' . $filename3;
                file_put_contents($file_to_save, $output);
                unset($dompdf);
                
                $save_name_file[] = $filename1;
                $save_name_file[] = $filename2;
                $save_name_file[] = $filename3;
            }
            
            $total = count($save_name_file);
            $all   = $total - 1;
            
            
            
            $html4 = $this->load->view('pembayaran/template_surat_permohonan', @$content, true);
            
            //create pdf
            $post['paper'] = 'A4';
            // require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
            // $dompdf = new Dompdf\Dompdf();
            //            require_once("application/libraries/dompdf/dompdf_config.inc.php");        
            $dompdf        = new Dompdf\Dompdf();
            $dompdf->load_html($html4);
            //$dompdf->set_paper('legal', 'portrait');
            if ($post['paper'] == 'f4') {
                $paper_size = array(
                    0,
                    0,
                    612.00,
                    936.00
                ); //opick
                $dompdf->set_paper($paper_size);
            } else {
                $dompdf->set_paper($post['paper'], 'portrait');
            }
            $dompdf->render();
            $output = $dompdf->output();
            
            $filename4    = 'surat_permohonan_pembayaran_' . $id_pembayaran . '.pdf';
            $file_to_save = 'assets/file/surat/' . $filename4;
            file_put_contents($file_to_save, $output);
            unset($dompdf);
            
            
            $html5 = $this->load->view('pembayaran/template_bapembayaran', @$content, true);
            
            //create pdf
            $post['paper'] = 'A4';
            // require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
            // $dompdf = new Dompdf\Dompdf();
            // require_once("application/libraries/dompdf/dompdf_config.inc.php");            
            $dompdf        = new Dompdf\Dompdf();
            $dompdf->load_html($html5);
            if ($post['paper'] == 'f4') {
                $paper_size = array(
                    0,
                    0,
                    612.00,
                    936.00
                ); //opick
                $dompdf->set_paper($paper_size);
            } else {
                $dompdf->set_paper($post['paper'], 'portrait');
            }
            $dompdf->render();
            $output = $dompdf->output();
            
            $filename5    = 'surat_ba_pembayaran_' . $id_pembayaran . '.pdf';
            $file_to_save = 'assets/file/surat/' . $filename5;
            file_put_contents($file_to_save, $output);
            unset($dompdf);
            
            
            $html6 = $this->load->view('pembayaran/template_kwitansi_pembayaran', @$content, true);
            
            //create pdf
            $post['paper'] = 'A4';
            // require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
            // $dompdf = new Dompdf\Dompdf();
            //require_once("application/libraries/dompdf/dompdf_config.inc.php");            
            $dompdf        = new Dompdf\Dompdf();
            $dompdf->load_html($html6);
            //$dompdf->set_paper('legal', 'portrait');
            // if ($post['paper'] == 'f4') {
            //     $paper_size = array(
            //         0,
            //         0,
            //         612.00,
            //         936.00
            //     ); //opick
            //     $dompdf->set_paper($paper_size);
            // } else {
            //     $dompdf->set_paper($post['paper'], 'portrait');
            // }
            // $dompdf->render();
            // $output = $dompdf->output();
            
            $filename6    = 'kwitansi_pembayaran_' . $id_pembayaran . '.pdf';
            // $file_to_save = 'assets/file/surat/' . $filename6;
            // file_put_contents($file_to_save, $output);
            
            
            
            //marge pdf
            ob_start();
            require_once("application/libraries/PDFMerger.php");
            $filename_marge = 'ba_pencairan_' . $id_pembayaran . '.pdf';
            $pdf            = new PDFMerger;
            
            $pdf->addPDF('assets/file/surat/' . $filename6, 'all');
            $pdf->addPDF('assets/file/surat/' . $filename5, 'all');
            $pdf->addPDF('assets/file/surat/' . $filename4, 'all');
            
            for ($i = $all; $i >= 0; $i = $i - 1) {
                $pdf->addPDF('assets/file/surat/' . $save_name_file[$i], 'all');
            }
            $pdf->merge('file', 'assets/file/surat/' . $filename_marge);
            
            //marge pdf
            // file_put_contents($file_to_save, $output);    
            // $dompdf->stream($filename_marge, array("Attachment" => false));
            // $pdf->Output('F',$filename_marge); 
            // redirect('assets/file/surat/' . $filename_marge);
            echo base_url().'assets/file/surat/' . $filename_marge;
            //create pdf
            
            // echo '<pre>';
            // var_dump($save_name_file);
            // echo '</pre>';
        }
        else {
            echo false;
        }
    }
}