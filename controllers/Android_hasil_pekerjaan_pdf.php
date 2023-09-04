<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Created by ERGUN.
 * 03/08/2018
 * Controller untuk generate pdf dari android
 */

class Android_hasil_pekerjaan_pdf extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->model('pembayaran_model', 'pembayaran');
        $this->load->model('pencairan_model', 'pencairan');
        $this->load->model('penyedia_model', 'penyedia');
        $this->load->model('hasil_pekerjaan_model', 'hasil_pekerjaan');
        $this->load->model('spk_model', 'spk');
        $this->db_ba = $this->load->database('db_pembayaran_ls', TRUE);
    }
    
    public function hasil_pekerjaan($id_hasil_pekerjaan = '') {
        
        if(!empty($id_hasil_pekerjaan)){    
            
            $content['data'] = $this->hasil_pekerjaan->get_by_id_detail_hasil_pekerjaan($id_hasil_pekerjaan);
            $html1 = $this->load->view('hasil_pekerjaan/template_surat_penyerahan_hasil_pekerjaan', @$content, true);
            $html2 = $this->load->view('hasil_pekerjaan/template_bas', @$content, true);
            $html3 = $this->load->view('hasil_pekerjaan/template_bast', @$content, true);
            
            //create pdf
            $post['paper'] = 'A4';          
            
            require_once("application/libraries/dompdf083/dompdf/autoload.inc.php");
            $dompdf = new Dompdf\Dompdf();
            
            // require_once("application/libraries/dompdf/dompdf_config.inc.php");          
            // $dompdf = new DOMPDF();          
            $dompdf->load_html($html1);      
            //$dompdf->set_paper('legal', 'portrait');
            if($post['paper'] == 'f4'){
                $paper_size = array(0,0,612.00,936.00); //opick
                $dompdf->set_paper($paper_size);
            }else{
                $dompdf->set_paper($post['paper'], 'portrait');
            }
            $dompdf->render();
            $output = $dompdf->output();                
            
            $filename1 = 'surat_penyerahan_hasil_pekerjaan_'.$id_hasil_pekerjaan .'.pdf';
            $file_to_save = 'assets/file/surat/'.$filename1;
            file_put_contents($file_to_save, $output);  
            unset($dompdf);
            
            
            $dompdf = new Dompdf\Dompdf();
            // $dompdf = new DOMPDF();      
            $dompdf->load_html($html2);      
            
            if($post['paper'] == 'f4'){
                $paper_size = array(0,0,612.00,936.00); //opick
                $dompdf->set_paper($paper_size);
            }else{
                $dompdf->set_paper($post['paper'], 'portrait');
            }
            $dompdf->render();
            $output = $dompdf->output();                
            
            $filename2 = 'surat_berita_acara_penerimaan_hasil_pekerjaan_'.$id_hasil_pekerjaan .'.pdf';
            $file_to_save = 'assets/file/surat/'.$filename2;
            file_put_contents($file_to_save, $output);          
            unset($dompdf);
            
            $dompdf = new Dompdf\Dompdf();
            // $dompdf = new DOMPDF();      
            $dompdf->load_html($html3);      
            
            if($post['paper'] == 'f4'){
                $paper_size = array(0,0,612.00,936.00); //opick
                $dompdf->set_paper($paper_size);
            }else{
                $dompdf->set_paper($post['paper'], 'portrait');
            }
            $dompdf->render();
            $output = $dompdf->output();                
            
            $filename3 = 'surat_berita_acara_serah_terima_pekerjaan_'.$id_hasil_pekerjaan.'.pdf';
            $file_to_save = 'assets/file/surat/'.$filename3;
            file_put_contents($file_to_save, $output);          
            
            
            //marge pdf
            ob_start();
            require_once("application/libraries/PDFMerger.php");
            $filename_marge = 'surat_hasil_pekerjaan_'.$id_hasil_pekerjaan.'.pdf';
            $pdf = new PDFMerger;
            $pdf->addPDF('assets/file/surat/'.$filename3, 'all')
                ->addPDF('assets/file/surat/'.$filename2, 'all')
                ->addPDF('assets/file/surat/'.$filename1, 'all')
                ->merge('file', 'assets/file/surat/'.$filename_marge);  

            echo base_url().'assets/file/surat/' . $filename_marge;
        }
        else {
            echo false;
        }
    }
}