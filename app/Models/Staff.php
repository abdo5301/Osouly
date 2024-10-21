<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;

class Staff extends Authenticatable
{

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $table = 'staff';
    public $timestamps = true;

    use SoftDeletes,Notifiable;

    public $modelPath = 'App\Models\Staff';
    protected $dates = ['lastlogin','created_at','updated_at','deleted_at'];
    protected $fillable = [
        'firstname',
        'lastname',
        'national_id',
        'email',
        'mobile',
        'avatar',
        'gender',
        'birthdate',
        'national_id',
        'address',
        'password',
        'remember_token',
        'description',
        'job_title',
        'status',
        'language_id',
        'permission_group_id',
        'supervisor_id',
        'indebtedness',
        'menu_type',
        'lastlogin',
        'language',
        'language_data_key'
    ];
    protected $hidden = array('password', 'remember_token');

    public function reminders(){
        return $this->hasMany('App\Models\Reminder','staff_id')
            ->orderByDesc('date_time');
    }


    public function importerData(){
        return $this->hasMany('App\Models\ImporterData','staff_id');
    }

    public function ticketComments(){
        return $this->hasMany('App\Models\TicketComment','staff_id');
    }


    public function contractTemplates(){
        return $this->hasMany('App\Models\ContractTemplate','staff_id');
    }


    public function calls(){
        return $this->hasMany('App\Models\Call','created_by_staff_id')
            ->select([
            'id',
            'client_id',
            'call_purpose_id',
            'call_status_id',
            'type',
            'description',
            'created_by_staff_id',
            'created_at'
        ])
            ->orderByDesc('id')
            ->with([
                'client',
                'call_purpose',
                'call_status',
                'staff'
            ]);
    }


    public static function StaffPerms($staffID){
        return Staff::find($staffID)->permission->pluck('route_name');
    }

    public function getFullnameAttribute($key)
    {
        if(isset($this->firstname) && strlen($this->firstname))
            $name = $this->firstname;
        if(isset($this->middlename) && strlen($this->middlename))
            $name .= ' ' .$this->middlename;

        if(isset($this->lastname) && strlen($this->lastname))
            $name .= ' ' .$this->lastname;

        return $name;
    }

    public function is_supervisor(){
        return $this->permission_group->is_supervisor == 'yes';
    }

    public function managed_staff(){
        return $this->hasMany('App\Models\Staff','supervisor_id')->where('status','active');
    }


    public function managed_staff_ids(){
        if($this->permission_group->is_supervisor == 'no')
            return [$this->id];

        $data = $this->hasMany('App\Models\Staff','supervisor_id')->where('status','active')->get(['id']);
        if(!$data)
            return [$this->id];

        $data = $data->toArray();

        return array_merge([$this->id],array_column($data,'id'));
    }

    public function permission_group(){
        return $this->belongsTo('App\Models\PermissionGroup','permission_group_id');
    }

    public function permission(){
        return $this->hasManyThrough('App\Models\Permission','App\Models\PermissionGroup','id','permission_group_id','permission_group_id');
    }


    public function pay(){
        return $this->hasMany('App\Models\Pay','staff_id');
    }

}