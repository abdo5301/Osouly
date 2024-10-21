<?php

namespace App\Modules\Api\Transformers;


class HomeTransformer extends Transformer
{
    public function transform($item, $opt)
    {

    }

    public function board($item, $opt)
    {

        return [
            "title" => $item['title'],
            "description" => $item['description'],
            'image' => asset($item['image'])
        ];

    }

    public function slider($item, $opt)
    {

        return [
            "title" => $item['title'],
            "description" => $item['description'],
            "video_url" => $item['video_url'],
            "url" => $item['url'],
            'image' => asset($item['image'])
        ];

    }

    public function page($item, $opt)
    {

        return [
            "title" => $item['title'],
            "content" => $item['content'],
            "video_url" => $item['video_url'],
            "meta_key" => $item['meta_key'],
            "meta_description" => $item['meta_description'],
            'images' => $this->image($item['images'])
        ];

    }

    public function services($item, $opt)
    {
        $content = mb_convert_encoding(substr(strip_tags($item['content']), 0, 200), 'UTF-8', 'UTF-8');

        return [
            "id" => $item['id'],
            "title" => $item['title'],
            "content" => $content . '...',
            "slug" => $item['slug'],
            "meta_key" => $item['meta_key'],
            "meta_description" => $item['meta_description'],
            "price" => amount($item['price'], true),
            "offer" => amount($item['offer'], true),
            "duration" => $item['duration'],
            "count" => $item['count'],
            "price" => $item['price'],
            'image' => $this->image($item['images'])
        ];

    }

    public function service_packages($items)
    {
        $arr = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                $details = [];

                if (!empty($item['properties_count'])) {
                    $details[] = ['key'=>__('properties count'),'value'=>$item['properties_count']];
                }

                if (!empty($item['type_count'])) {
                    $details[] = ['key'=>__('Count Per Day'),'value'=>$item['type_count']];
                }

                if (!empty($item['price'])) {
                    $details[] = ['key'=>__('service price'),'value'=>amount($item['price'])];
                }


                if (!empty($item['offer'])) {
                    $details[] = ['key'=>__('Price after offer'),'value'=>amount($item['offer'])];
                }


                if (!empty($item['percentage'])) {
                    $details[] = ['key'=>__('percentage from Paid'), 'value'=>$item['percentage'].' %'];
                }

                if (!empty($item['duration'])) {
                    $details[] = ['key'=>__('duration'),'value'=>$item['duration'].__(' Days')];
                }

                if(  (strtotime(date('Y-m-d')) > strtotime($item['discount_from']) &&  strtotime(date('Y-m-d')) < strtotime($item['discount_to']) ) && !empty($item['discount_value'])){
                    $details[] = ['key'=>__('discount'),'value'=>($item['discount_type'] == 'fixed')?amount($item['discount_value']):$item['discount_value'].' %'];
                    $details[] = ['key'=>__('Price after Discount'),
                        'value'=>($item['discount_type'] == 'fixed')?amount($item['price']-$item['discount_value']):amount($item['price'] -  (double)($item['price']/100 *$item['discount_value'])) ];
                }

                if (!empty($item['content_'.lang()])) {
                    $details[] = ['key'=>'content' ,'value'=>$item['content_'.lang()]];
                }

                $arr[] = [
                    "id" => $item['id'],
                    "title" => strip_tags($item['title_' . lang()]),
                    'details' => $details
                    ];
            }
        }
        return $arr;

    }

    public function service_details($item, $opt)
    {

//        if(strtotime(date('Y-m-d')) > strtotime($item['discount_from']) &&
//            strtotime(date('Y-m-d')) < strtotime($item['discount_to']) ){
//
//        }

        return [
            "id" => $item['id'],
            "title" => $item['title_' . lang()],
            "content" => $item['content_' . lang()],
            "slug" => $item['slug_' . lang()],
            "meta_key" => $item['meta_key_' . lang()],
            "meta_description" => $item['meta_description_' . lang()],
            'image' => $this->image($item['images']),
            'package' => $this->service_packages($item['packages'])

        ];

    }

}