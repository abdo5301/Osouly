<?php

namespace App\Modules\Api\Transformers;


class MaintenanceTransformer extends Transformer
{
    public function transform($item,$opt)
    {

        return[
            'id'=>$item['id'],
            'category_id'=>$item['maintenance_category_id'],
            'category_name'=>$item['category']['name_'.lang()],
            'priority'=>$item['priority'],
            'notes'=>$item['notes'],
            'date'=>$item['date'],
            'status'=>__($item['status']),
            'total_work'=>$item['total_work'],
            'total_item'=>$item['total_item'],
            'work_details'=>$item['work_details'],
            'item_details'=>$item['item_details'],
            'created_at'=>$item['created_at']
        ];

    }



}