<?php

namespace App\Modules\Api\Transformers;


class ClientTransformer extends Transformer
{
    public function transform($item,$opt)
    {

    }

    public function users($item){
        return [
            'id'=>$item['id'],
            'first_name'=>$item['first_name'],
            'second_name'=>$item['second_name'],
            'mobile'=>$item['mobile'],
            'permissions'=>$item['permissions']
        ];
    }

    public function jobs($item){

        return [
          'job_title'=>$item['job_title'],
          'company_name'=>$item['company_name'],
          'from_date'=>$item['from_date'],
          'to_date'=>$item['to_date'],
          'present'=>$item['present']
        ];
    }

    public function tickets($item){
        return [
            'id'=>$item['id'],
            'title'=>$item['title'],
            'status'=>$item['status'],
            'created_at'=>$item['created_at']
        ];
    }


    public function ticket_details($item){

        return [
            'id'=>$item['id'],
            'title'=>$item['title'],
            'status'=>$item['status'],
            'created_at'=>$item['created_at'],
            'comments'=>$this->ticket_comments($item['comments'])
        ];
    }

    function ticket_comments($comments){

        $comments_array = [];
        if(!empty($comments)){
            foreach ($comments as $value){
        if(!empty($value['staff_id'])){
            $type = 'admin';
        }else{
            $type = 'client';
        }
                $comments_array[] = [
                  'staff_name' =>(!empty($value['staff_id']))?$value['staff']['firstname'].' '.$value['staff']['lastname']:'--',
                  'client_name' =>(!empty($value['client_id']))?$value['client']['first_name'].' '.$value['client']['second_name']:'--',
                  'comment' =>$value['comment'],
                  'type' =>$type,
                  'image' =>(!empty($value['image']))?asset($value['image']):''
                ];
            }
        }
        return $comments_array;
    }






}