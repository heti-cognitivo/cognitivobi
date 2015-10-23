<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bi_Report extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'id_bi_report';
    protected $table = 'bi_report';
    public function Bi_Report_Details(){
      return $this->hasMany('App\Bi_Report_Detail','id_bi_report','id_bi_report');
    }
}
