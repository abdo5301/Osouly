<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Traits\LogsActivity;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'clients';
    public $timestamps = true;

    use LogsActivity;
    protected static $logAttributes = ['*'];

    protected $dates = ['created_at','updated_at'/*,'deleted_at'*/];
    protected $fillable = [
        'type',
        'first_name',
        'second_name',
        'third_name',
        'last_name',
        'gender',
        'birth_date',
        'id_number',
        'area_id',
        'activation_code',
        'forgot_password_code',
        'credit',
        'parent_id',
        'permissions',
        'email',
        'phone',
        'mobile',
        'password',
        'address',
        'description',
        'status',
        'bank_account_number',
        'firebase_token',
        'verified_at',
        'created_by_staff_id',
        'bank_code',
        'branch_code',
    ];


    public static function block(){
        return static::select( 'id','type','first_name', 'second_name', 'third_name', 'last_name', 'gender', 'birth_date','verified_at','mobile','parent_id','permissions');
    }

    public function getFullnameAttribute($key)
    {
        $name = '';
        if(isset($this->first_name) && strlen($this->first_name))
            $name .= $this->first_name;

        if(isset($this->second_name) && strlen($this->second_name))
            $name .= ' ' .$this->second_name;

        if(isset($this->third_name) && strlen($this->third_name))
            $name .= ' ' .$this->third_name;

        if(isset($this->last_name) && strlen($this->last_name))
            $name .= ' ' .$this->last_name;

        return $name;
    }

    public function contact_us(){
        return $this->hasMany('App\Models\Contactus','client_id');
    }

    public function client_packeges(){
        return $this->hasMany('App\Models\ClientPackages','client_id');
    }

    public function credit_transactions(){
        return $this->hasMany('App\Models\CreditTransactions','client_id');
    }


    public function notification(){
        return $this->hasMany('App\Models\Notification','client_id');
    }

    public function maintenance(){
        return $this->hasMany('App\Models\Maintenance','client_id');
    }

    public function area(){
        return $this->belongsTo('App\Models\Area','area_id');
    }

    public function created_by(){
        return $this->belongsTo('App\Models\Staff','created_by_staff_id');
    }

    public function renterRequests(){
        return $this->hasMany('App\Models\Request','renter_id');
    }

    public function clientTransactions(){
        return $this->hasMany('App\Models\ClientTransaction','client_id');
    }

    public function canceledContracts(){
        return $this->hasMany('App\Models\Contract','canceled_by_client_id');
    }

    public function renterContracts(){
        return $this->hasMany('App\Models\Contract','renter_id');
    }

    public function contractTemplates(){
        return $this->hasMany('App\Models\ContractTemplate','owner_id');
    }

    public function sms(){
        return $this->hasMany('App\Models\Sms','client_id');
    }

    public function Tickets(){
        return $this->hasMany('App\Models\Ticket','client_id');
    }

    public function comments(){
        return $this->hasMany('App\Models\TicketComment','client_id');
    }

    public function favorite(){
        return $this->hasMany('App\Models\PropertyFavorite','client_id');
    }

    public function jobs(){
        return $this->hasMany('App\Models\ClientJob','client_id');
    }

    public function packages(){
        return $this->hasMany('App\Models\ClientPackages','client_id');
    }

    public function property(){
        return $this->hasMany('App\Models\Property','owner_id');
    }

    public function calls(){
        return $this->hasMany('App\Models\Call','client_id')
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

    public function reminders(){
        return $this->morphMany('App\Models\Reminder','sign')
            ->orderByDesc('date_time')
            ->with([
                'staff'
            ]);
    }

    public function images(){
        return $this->morphMany('App\Models\Image','sign');
    }


    public function findForPassport($username){
        return $this->where('mobile', $username)->first();
    }


    public function pay(){
        return $this->hasMany('App\Models\Pay','client_id');
    }


    public function paid_payments(){
        return $this->hasMany('App\Models\Payments','paid_client_id');
    }

    public function owner_payments(){
        return $this->hasMany('App\Models\Payments','owner_id');
    }

    public function owner_installments(){
        return $this->hasMany('App\Models\Installment','owner_id');
    }

    public function renter_installments(){
        return $this->hasMany('App\Models\Installment','renter_id');
    }

    public function invoices(){
        return $this->hasMany('App\Models\Invoice','client_id');
    }

    public function paid_invoice_details(){
        return $this->hasMany('App\Models\InvoiceDetails','paid_client_id');
    }

    public function invoice_details_comments(){
        return $this->hasMany('App\Models\InvoiceDetailsComments','client_id');
    }

}