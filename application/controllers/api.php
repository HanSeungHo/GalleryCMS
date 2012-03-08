<?php

if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Api extends MY_Controller
{
  public function upload($album_id)
  {
    $config['upload_path'] = './uploads/';
    $config['allowed_types'] = 'gif|jpg|png';
    $config['max_size'] = '2048'; // 2MB
    $config['overwrite'] = TRUE;
    $config['remove_spaces'] = TRUE;
    $config['encrypt_name'] = FALSE;
    $config['overwrite'] = FALSE;
    
    $this->load->library('upload', $config);
    
    if (!$this->upload->do_upload('Filedata'))
    {
      error_log($this->upload->display_errors());
      echo $this->upload->display_errors();
    }
    else
    {
      $upload_info = $this->upload->data();

       // Insert file information into database
       $this->load->model('image_model', 'Image_Model');
       
       $now = date('Y-m-d H:i:s');
       $image_data = array(
        'album_id'       => $album_id,
        'uuid'           => $this->create_uuid(),
        'name'           => $upload_info['file_name'],
        'order_num'      => 0,
        'caption'        => '',
        'raw_name'       => $upload_info['raw_name'],
        'file_type'      => $upload_info['file_type'],
        'file_name'      => $upload_info['file_name'],
        'file_ext'       => $upload_info['file_ext'],
        'file_size'      => $upload_info['file_size'],
        'path'           => $config['upload_path'],
        'full_path'      => $upload_info['full_path'],
        'published'      => 0,
        'created_at'     => $now,
        'created_by'     => $this->input->post('user_id')
       );
       $this->Image_Model->create($image_data);
    }
    
    echo $upload_info['file_name'];
  }

  public function resize($filename)
  {
    $config['image_library'] = 'gd2';
    $config['source_image'] = './uploads/' . $filename;
    $config['create_thumb'] = TRUE;
    $config['maintain_ratio'] = TRUE;
    $config['width']   = 75;
    $config['height'] = 50;
    
    $this->load->library('image_lib', $config); 
    
    if ($this->image_lib->resize())
    {
      echo 'success';
    } else {
      echo 'failure';
    }
  }
}
  