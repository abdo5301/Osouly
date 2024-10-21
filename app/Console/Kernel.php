<?php

namespace App\Console;

use App\Models\Contract;
use App\Models\Importer;
use App\Models\Invoice;
use App\Models\Staff;
use App\Models\Image;
use App\Models\Campaign;
use App\Models\Newsletter;
use App\Models\ImporterData;
use App\Models\Property;
use App\Models\Request;
use App\Models\Reminder;
use Carbon\Carbon;
use Auth;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //

    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        echo 'sss';
        set_time_limit(0);
        // $schedule->command('inspire')
        //          ->hourly();

        // Importer
        $schedule->call(function () {
            include app_path('Libs/DataImport/simple_html_dom.php');

            $data = Importer::where('status', 'pending')->first();
            if (!$data) {
                return false;
            }

            $data->update([
                'status' => 'proccess'
            ]);

            $importer = new \App\Libs\DataImport\Importer($data->connector);
            $importer->setImporterModal($data);
            switch ($data->connector) {
                case 'OLX':
                    if ($data->area->olx_id) $importer->setArea($data->area->olx_id);
                    if ($data->property_type->olx_id) $importer->setType($data->property_type->olx_id);
                    if ($data->purpose->olx_id) $importer->setPurpose($data->purpose->olx_id);
                    if ($data->space_from || $data->space_to) $importer->setSpace($data->space_from, $data->space_to);
                    if ($data->price_from || $data->price_to) $importer->setPrice($data->price_from, $data->price_to);
                    if ($data->query_name) $importer->setQueryName($data->query_name);
                    break;
            }

            $importer->getList($data->page_start, $data->page_end)->getProperties();

        })->everyMinute();


        //-------clear null property images (delete images with no sign id)
        // daily action [clear null images from image table]
        $schedule->call(function () {
            $emptyImages = Image::whereNull('sign_id')->get();
            if ($emptyImages->isNotEmpty()) {
                foreach ($emptyImages as $key => $value) {
                    if (is_file($value->path))
                        unlink($value->path);
                }
                Image::whereNull('property_id')->delete();
            }
            //-------clear null property images

        })->dailyAt('12:00'); // at 12 after none


        // daily action [send new campaign]
        $schedule->call(function () {

            $campaign = Campaign::where('status', 'new')->first();
            $emails = Newsletter::get()->count();

            if ($campaign) {


                $campaign->update(['status' => 'progress']);
                if ($emails->isNotEmpty()) {
                    $sender_counter = 0;
                    for ($x = 0; $x <= $emails; $x++) {
                        $limit = 20;
                        $offset = $limit * $x;
                        $send_emails = Newsletter::skip($offset)->limit($limit)->get();

                        //start send mail
                        foreach ($send_emails as $single_email) {
                            $email = $single_email->email;
                            ///send here
                            send_email($email, $campaign->title, $campaign->content);
                            $sender_counter++;
                        }
                        //end send mail

                        $campaign->update(['sent' => $sender_counter]);

                        if (count($emails) <= $offset) {
                            $campaign->update(['status' => 'done']);
                            break;
                        } else {
                            sleep(5);
                        }
                    }
                }
            }

        })->everyFiveMinutes();


        $schedule->call(function () {

            // انتهاء العقود
            $contracts = Contract::where('status', 'active')->where('date_to', date('Y-m-d'))->with('property')->get();
            if ($contracts->isNotEmpty()) {
                foreach ($contracts as $row) {
                    $row->update([
                        'status' => 'expire'
                    ]);
                }
            }




            // الايجارات
            $contracts = Contract::where('status', 'active')->where('pay_from', '<=', date('Y-m-d'))
                ->where('pay_to', '>=', date('Y-m-d'))->with('property')->get();

            if ($contracts->isNotEmpty()) {
                foreach ($contracts as $row) {
                    $last_invoice = Invoice::where(['property_id' => $row->property_id,
                        'property_due_id' => $row->property->dues()->where('due_id', 1)->first()->id,'client_id'=>$row->renter_id,
                        'contract_id'=>$row->id])
                        ->orderby('id', 'desc')->first();
                    $invoice = [];
                    if (!$last_invoice) {
                        $due_date_to_pay = $row->pay_from;
                        if ($row->pay_at == 'end') {
                            $due_date_to_pay = date('Y-m-d', strtotime("+27 days", strtotime($row->pay_from)));
                        }
                        if (date('Y-m-d') >= $due_date_to_pay) {


                            $d1 = new \DateTime(date('Y-m-d'));
                            $d2 = new \DateTime($due_date_to_pay);

                            $diff = $d2->diff($d1);
                            $months = ($diff->y * 12) + $diff->m;
                            if ($months > 0) {
                                for ($i = 1; $i <= $months; $i++) {
                                    $price = $row->price;
                                    if ($i != 1) {
                                        $due_date_to_pay = date('Y-m-d', strtotime("+1 month", strtotime($due_date_to_pay)));
                                    }
                                    if (strtotime($row->increase_from) <= strtotime($due_date_to_pay)) {
                                        $d1 = new \DateTime($due_date_to_pay);
                                        $d2 = new \DateTime($row->increase_from);

                                        $diff = $d2->diff($d1);
                                        if ($diff->y > 0) {
                                            $price = (double)$row->price + ( ((int)$diff->y+1) * (double)$row->increase_value);
                                        }else{
                                            $price = (double)$row->price +  (double)$row->increase_value;
                                        }
                                    }
                                    if (!empty($row->cut_from_insurance) && $row->deposit_rent >= $row->cut_from_insurance) {
                                        $price = $price - $row->cut_from_insurance;
                                        $row->update(['deposit_rent' => $row->deposit_rent - $row->cut_from_insurance]);
                                    }

                                    $invoice = Invoice::create([
                                        'owner_id' => $row->property->owner_id,
                                        'contract_id' => $row->id,
                                        'client_id' => $row->renter_id,
                                        'property_id' => $row->property_id,
                                        'property_due_id' => @$row->property->dues()->where('due_id', 1)->first()->id,
                                        'amount' => $price,
                                        'date' => $due_date_to_pay,
                                        'status' => 'unpaid',
                                    ]);
                                }
                            }
                        }

                    } else {
                        $due_date_to_pay = date('Y-m-d', strtotime("+" . $row->pay_every . " month", strtotime($last_invoice->date)));
                        echo ($due_date_to_pay);
                        if (strtotime(date('Y-m-d')) >= strtotime($due_date_to_pay)) {

                            $d1 = new \DateTime(date('Y-m-d'));
                            $d2 = new \DateTime($due_date_to_pay);
                            $d3 = new \DateTime($last_invoice->date);

                            $diff = $d2->diff($d1);
                            $months = ($diff->y * 12) + $diff->m;

                            if ($months > 0) {
                                for ($i = 1; $i <= $months; $i++) {
                                    $price = $row->price;
                                    if ($i != 1) {
                                        $due_date_to_pay = date('Y-m-d', strtotime("+1 month", strtotime($due_date_to_pay)));
                                    }
                                    if (strtotime($row->increase_from) <= strtotime($due_date_to_pay)) {
                                        $d1 = new \DateTime($due_date_to_pay);
                                        $d2 = new \DateTime($row->increase_from);

                                        $diff = $d2->diff($d1);

                                        if ($diff->y > 0) {
                                            $price = (double)$row->price + ( ((int)$diff->y+1) * (double)$row->increase_value);
                                        }else{
                                            $price = (double)$row->price +  (double)$row->increase_value;
                                        }
                                    }
                                    if (!empty($row->cut_from_insurance)  && $row->deposit_rent >= $row->cut_from_insurance) {
                                        $price = $price - $row->cut_from_insurance;
                                        $row->update(['deposit_rent' => $row->deposit_rent - $row->cut_from_insurance]);
                                    }
                                    $invoice = Invoice::create([
                                        'owner_id' => $row->property->owner_id,
                                        'client_id' => $row->renter_id,
                                        'contract_id' => $row->id,
                                        'property_id' => $row->property_id,
                                        'property_due_id' => @$row->property->dues()->where('due_id', 1)->first()->id,
                                        'amount' => $price,
                                        'date' => $due_date_to_pay,
                                        'status' => 'unpaid',
                                    ]);
                                }
                            }elseif($d1->diff($d3)->m >= 1 ){
                                $price = $row->price;

                                if (strtotime($row->increase_from) <= strtotime($due_date_to_pay)) {
                                    $d1 = new \DateTime($due_date_to_pay);
                                    $d2 = new \DateTime($row->increase_from);

                                    $diff = $d2->diff($d1);

                                    if ($diff->y > 0) {
                                        $price = (double)$row->price + ( ((int)$diff->y+1) * (double)$row->increase_value);
                                    }else{
                                        $price = (double)$row->price +  (double)$row->increase_value;
                                    }
                                }
                                if (!empty($row->cut_from_insurance)  && $row->deposit_rent >= $row->cut_from_insurance) {
                                    $price = $price - $row->cut_from_insurance;
                                    $row->update(['deposit_rent' => $row->deposit_rent - $row->cut_from_insurance]);
                                }
                                $invoice = Invoice::create([
                                    'owner_id' => $row->property->owner_id,
                                    'client_id' => $row->renter_id,
                                    'contract_id' => $row->id,
                                    'property_id' => $row->property_id,
                                    'property_due_id' => @$row->property->dues()->where('due_id', 1)->first()->id,
                                    'amount' => $price,
                                    'date' => $due_date_to_pay,
                                    'status' => 'unpaid',
                                ]);
                            }
                        }
                    }

                    if (!empty($invoice)) {
                        push_notification('فاتورة ايجار الشهر', $row->client_id);
                    }

                }
            }


            // الاستحقاقات
            foreach ($contracts as $one_contract) {
                $property_dues = $one_contract->property->dues()->where('due_id', '!=', 1)->get();

                foreach ($property_dues as $due) {
                    $last_due_invoice = Invoice::where('client_id', $one_contract->renter_id)
                        ->where('property_due_id', $due->id)
                        ->where('contract_id', $one_contract->id)
                        ->orderby('id', 'desc')->first();


                    if (!$last_due_invoice) {
                        $due_date_to_pay = $row->pay_from;
                    }
                    $create = false;

                    if ($due->duration == 'one_time') {

                        if (!$last_due_invoice) {
                            $create = 1;
                        }
                    } elseif ($due->duration == 'day') {
                        if (!$last_due_invoice) {
                            $create = 1;
                        } else {
                            if (strtotime(date('Y-m-d')) > strtotime($last_due_invoice->date)) {
                                $create = 1;
                                $due_date_to_pay = date('Y-m-d', strtotime("+1 day", strtotime($last_due_invoice->date)));
                            }
                        }
                    } elseif ($due->duration == 'month') {
                        if (!$last_due_invoice) {
                            if (strtotime(date('Y-m-d')) > strtotime($one_contract->date_from . '+1 month')) {
                                $create = 1;
                            }
                        } else {
                            if (strtotime(date('Y-m-d')) > strtotime($last_due_invoice->date . '+1 month')) {
                                $create = 1;
                                $due_date_to_pay = date('Y-m-d', strtotime("+1 month", strtotime($last_due_invoice->date)));
                            }
                        }
                    } elseif ($due->duration == 'year') {
                        if (!$last_due_invoice) {
                            if (strtotime(date('Y-m-d')) > strtotime($one_contract->date_from . '+1 year')) {
                                $create = 1;
                            }
                        } else {
                            if (strtotime(date('Y-m-d')) > strtotime($last_due_invoice->date . '+1 year')) {
                                $create = 1;
                                $due_date_to_pay = date('Y-m-d', strtotime("+1 year", strtotime($last_due_invoice->date)));
                            }
                        }

                    }

                    if ($create) {

                        Invoice::create([
                            'owner_id' => $one_contract->property->owner_id,
                            'client_id' => $one_contract->renter_id,
                            'contract_id' => $one_contract->id,
                            'property_id' => $one_contract->property_id,
                            'property_due_id' => $due->id,
                            'amount' => $due->value,
                            'status' => 'unpaid',
                            'date' => $due_date_to_pay
                        ]);
                    }

                }
            }


        })->everyMinute();


    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
