<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectImgUpload extends Model
{
    use HasFactory;
    protected $primaryKey = 'proj_img_id';

    public function project(){
      return $this->belongsTo('App\Models\ProjectInfo','project_id','proj_id');
    }

    public function uploader(){
      return $this->belongsTo('App\Models\User','uploaded_by_id','id');
    }

}
