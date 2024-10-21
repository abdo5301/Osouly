<?php

include('simple_html_dom.php');

class Olx{

	public static function getList($siteURL,$startPage = 1,$endPage = 1){
		$urls = [];

		for($i = $startPage; $i<=$endPage;$i++){
			if($i == 1){
				$html  = file_get_html($siteURL);
			}else{
				$html  = file_get_html($siteURL.'?page='.$i);
			}

			foreach ($html->find('.ads__item') as $key => $value) {
				preg_match("#\'(.*)\'#", $value->onclick,$path);
				
				$urls[] = $path[1];
			}
		}

		return $urls;	 

	}

	public static function getOneProperty($url){

		preg_match('#ID(.*)\.html#', $url,$match);
		$AdsID = $match[1];

		$html  = file_get_html($url);


		$name  = trim($html->find('.lheight26',0)->plaintext);
		$price = trim(str_replace([',','ج.م'], '', $html->find('.xxxx-large',1)->plaintext));
		$description  = trim($html->find('.lheight20',0)->plaintext);
		$ownerName = trim( $html->find('.user-box__info__name',0)->plaintext);


		// -- Options

		$space    = 0;
		$bedRooms = 0;
		$bathRoom = 0;

		foreach ($html->find('table .item') as $key => $value) {
			$optionName = $value->find('tr',0)->find('th',0)->plaintext;
			$optionValue = trim($value->find('tr',0)->find('td',0)->find('strong',0)->plaintext);
			switch ($optionName) {
				case 'المساحة (م٢)':
					$space = $optionValue;	
					break;

				case 'غرف نوم':
					$bedRooms = $optionValue;	
					break;

				case 'الحمامات':
					$bathRoom = $optionValue;	
					break;
			}

		}

		// -- Options

		// Mobile Number
		$mobile = @json_decode(file_get_contents('https://olx.com.eg/ajax/misc/contact/phone/'.$AdsID.'/'));
		$mobile = str_replace(' ', '', $mobile->value);
		// Mobile Number

		return [
			'id' => $AdsID,
			'name' => $name,
			'price' => $price,
			'description' => $description,
			'space' => $space,
			'bedRooms' => $bedRooms,
			'bathRoom' => $bathRoom,
			'mobile' => $mobile,
			'ownerName'=> $ownerName
		];

	}

}



class Aqarmap{

	public static function getList($siteURL,$startPage = 1,$endPage = 1){
		$urls = [];

		for($i = $startPage; $i<=$endPage;$i++){
			if($i == 1){
				$html  = file_get_html($siteURL);
			}else{
				$html  = file_get_html($siteURL.'?page='.$i);
			}

			foreach ($html->find('.listingItem') as $key => $value) {
				$path = $value->find('div',0)->find('div',0)->find('div',0)->find('div',0)->find('a',0)->href;
				if(strpos($path, '/ar/user/') === 0){
					$path = $value->find('div',0)->find('div',0)->find('div',0)->find('div',0)->find('a',1)->href;
				}

				$urls[] = 'https://egypt.aqarmap.com'.$path;
			}
		}

		return $urls;	 

	}

	public static function getOneProperty($url){

		$html  = file_get_html($url);
		$name  = trim($html->find('.titleAndAddress ',0)->find('div',0)->find('h1',0)->plaintext);
		$price = trim(str_replace([','], '', $html->find('.listing-price-content',0)->find('span',0)->plaintext));
		$description  = trim($html->find('#listingText',0)->plaintext);
		$ownerName = trim( $html->find('.sellerProperties',0)->find('h4',0)->plaintext);
		$mobile = urldecode(trim( $html->find('.phoneNumber',0)->{'data-number'}));




		// -- Options

		$space    = 0;
		$bedRooms = 0;
		$bathRoom = 0;

		foreach ($html->find('.listing_attributes',0)->find('label') as $key => $value) {
			

			$optionName  = trim(str_replace([0,1,2,3,4,5,6,7,8,9], '', $value->plaintext));
			$optionValue = (int) trim($value->plaintext);

			switch ($optionName) {
				case 'متر²':
					$space = $optionValue;	
					break;

				case 'غرف':
					$bedRooms = $optionValue;	
					break;

				case 'حمام':
					$bathRoom = $optionValue;	
					break;
			}

		}

		// -- Options

		return [
			'id' => explode('-', end(explode('/', $url)))[0],
			'name' => $name,
			'price' => $price,
			'description' => $description,
			'space' => $space,
			'bedRooms' => $bedRooms,
			'bathRoom' => $bathRoom,
			'mobile' => $mobile,
			'ownerName'=> $ownerName
		];

	}

}







$amr = Aqarmap::getList('https://egypt.aqarmap.com/ar/for-sale/property-type/cairo/6th-of-october/',1,1);

print_r($amr);