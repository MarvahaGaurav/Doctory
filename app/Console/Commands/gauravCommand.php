<?php

namespace App\Console\Commands;
use App\Http\Controllers;
use Log;
use Illuminate\Console\Command;
use App\Appointment;
use App\Notification;
use App\User;
use App\TimeSlot;
use \Carbon\Carbon;

class gauravCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gaurav:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Notification Crone Is Running..');

        // $apntmnt_list = Appointment::whereNotIn('status_of_appointment',['Pending','Expired','Cancelled','Completed'])->get();
        $apntmnt_list = Appointment::where('status_of_appointment','Accepted')
        ->where('appointment_date',date('Y-m-d'))->get();
       

        foreach ($apntmnt_list as $key => $value) {

            // Log::warning('Counter'.print_r($key,True));
            // Log::info('Appointment Detail-----------'.print_r($value,True));

            $doctor_device_token = User::where('id',$value->doctor_id)->first()->device_token;
            $patient_device_token = User::where('id',$value->patient_id)->first()->device_token;

            $Time_slot_detail = TimeSlot::find($value->time_slot_id);
            $Appointment_TimeSlot_StartTime = $Time_slot_detail->start_time;
            $Appointment_TimeSlot_EndTime = $Time_slot_detail->end_time;

            $obj = new \App\Http\Controllers\Controller;

            // Log::warning('------------------------------------------------------------------------');

            // Log::info('Appointment Time Start'.print_r(Carbon::parse($Appointment_TimeSlot_StartTime)->format('Y-m-d h:i'),True));

            // Log::info('Appointment Time Now'.print_r(Carbon::now('Asia/Riyadh')->addMinutes(15)->format('Y-m-d h:i'),True));

            if( Carbon::parse($Appointment_TimeSlot_StartTime)->format('Y-m-d h:i') ==  Carbon::now('Asia/Riyadh')->addMinutes(15) ->format('Y-m-d h:i') ){

                $NotificationDataArray_DR = [
                    'getter_id' => $value->doctor_id,
                    'message' => __('messages.notification_messages.Doctor_Have_Appointment_After_Fifteen_Minutes')
                ];

                Notification::insert(['doctor_id'=>$value->doctor_id,'patient_id'=>$value->patient_id,'type' =>__('messages.notification_status_codes.Doctor_Have_Appointment_After_Fifteen_Minutes'),'appointment_id' => $value->id]);

                $NotificationGetterDetail_DR = User::find($value->doctor_id);
                if($NotificationGetterDetail_DR->notification && !empty($NotificationGetterDetail_DR->remember_token)){
                    $obj->send_notification($NotificationDataArray_DR);
                }


                $NotificationDataArray_PT = [
                    'getter_id' => $value->patient_id,
                    'message' => __('messages.notification_messages.Patient_Have_Appointment_After_Fifteen_Minutes')
                ];

                Notification::insert(['doctor_id'=>$value->doctor_id,'patient_id'=>$value->patient_id,'type' =>__('messages.notification_status_codes.Patient_Have_Appointment_After_Fifteen_Minutes'),'appointment_id' => $value->id]);
                
                $NotificationGetterDetail_DR = User::find($value->patient_id);

                if($NotificationGetterDetail_DR->notification && !empty($NotificationGetterDetail_DR->remember_token)){
                    $obj->send_notification($NotificationDataArray_PT);
                }
            }

            /*Log::Warning('---------------GAURAV MARVAHA------------------------------');
            Log::info('start_time-----------'.print_r(Carbon::parse($Appointment_TimeSlot_StartTime )->format('Y-m-d h:i'),True));
            Log::info('Now Time-----------'.print_r(Carbon::now()->format('Y-m-d h:i'),True));


            Log::info(Carbon::parse($Appointment_TimeSlot_StartTime)->format('Y-m-d h:i') ==  Carbon::now()->format('Y-m-d h:i'));*/

            if( Carbon::parse($Appointment_TimeSlot_StartTime)->format('Y-m-d h:i') ==  Carbon::now('Asia/Riyadh')->format('Y-m-d h:i') ){
                $NotificationDataArray_DR = [
                    'getter_id' => $value->doctor_id,
                    'message' => __('messages.notification_messages.Appointment_Started')
                ];
                $NotificationGetterDetail_DR = User::find($value->doctor_id);
                if($NotificationGetterDetail_DR->notification && !empty($NotificationGetterDetail_DR->remember_token)){
                    $obj->send_notification($NotificationDataArray_DR);
                }

                $NotificationDataArray_PT = [
                    'getter_id' => $value->patient_id,
                    'message' => __('messages.notification_messages.Appointment_Started')
                ];
                $NotificationGetterDetail_DR = User::find($value->patient_id);
                if($NotificationGetterDetail_DR->notification && !empty($NotificationGetterDetail_DR->remember_token)){
                    $obj->send_notification($NotificationDataArray_PT);
                }
            }

        }
    }
}
