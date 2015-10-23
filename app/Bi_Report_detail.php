<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bi_Report_detail extends Model
{
  public $timestamps = false;
  protected $table = 'bi_report_detail';
  protected $primaryKey = 'id_bi_report_detail';
  public function Bi_Report(){
    return $this->belongsTo('App\Bi_Report');
  }
}
