<?php

namespace App\Http\Controllers\Homepage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomepageController extends Controller {
	
	public function generateUrl(Request $request) {
		
		$url = $request->get('user_url');
		if (!(strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0)) $url = 'http://' . $url;
		if(filter_var($url, FILTER_VALIDATE_URL) === false || !preg_match( '/^(http|https):\\/\\/[a-z0-9_]+([\\-\\.]{1}[a-z_0-9]+)*\\.[_a-z]{2,5}'.'((:[0-9]{1,5})?\\/.*)?$/i' ,$url)) {
			return json_encode(['success' => false, 'error_message' => 'Sorry! Inavlid URL']);
		}
		
		$existance = DB::table('urls')->where('long_url', $url)->select('long_url', 'hash')->get();
		if (isset($existance) && count($existance) > 0) {
			return json_encode(['success' => true, 'data' => [
				'long_url' => $existance[0]->long_url,
				'short_url' => url('/shorturl/' . $existance[0]->hash)
			]]);
		}
		
		$url_safety = $this->checkIsUrlSafe($url);
		if ($url_safety['success'] !== true) return json_encode(['success' => false, 'error_message' => 'Something went wrong! Cound not scan your URL for malware. Please try again later.']);
		if ($url_safety['result'] !== true) return json_encode(['success' => false, 'error_message' => 'Sorry!! Your URL is unsafe and contains malware.']);
		
		$new_hash = $this->generateUniqueHash();
		DB::table('urls')->insert([
			'long_url' => $url,
			'hash' => $new_hash,
			'created_at' => date('Y-m-d H:i:s')
		]);
		
		return json_encode(['success' => true, 'data' => [
			'long_url' => $url,
			'short_url' => url('/shorturl/' . $new_hash)
		]]);
		
	}
	
	
	private function generateUniqueHash() {
		$new_hash = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 6);
		while($this->checkHashExistance($new_hash)) {
			$new_hash = substr(str_shuffle("0123456789abcdefghijklmnopqrstvwxyz"), 0, 6);
		}
		return $new_hash;
	}
	
	private function checkHashExistance($hash) {
		$hash_existance = DB::table('urls')->where('hash', $hash)->select('hash')->get();
		if (isset($hash_existance) && count($hash_existance) > 0) return true;
		return false;
	}
	
	private function checkIsUrlSafe($user_url) {
		$api_key = env("GOOGLE_SAFE_BROWSING_API_KEY", "");
		$api_client_id = env("GOOGLE_SAFE_BROWSING_API_CLIENT_ID", "");
		$api_url = 'https://safebrowsing.googleapis.com/v4/threatMatches:find?key='. $api_key;
		
		$request = [
			'client' => [
				'clientId' => $api_client_id,
				'clientVersion' => '1.0.0'
			],
			'threatInfo' => [
				'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING'],
				'platformTypes' => ['WINDOWS'],
				'threatEntryTypes' => ['URL'],
				'threatEntries' => [
					['url' => $user_url]
				]
			]
		];
		
		try {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $api_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			$res = curl_exec($ch);
			curl_close($ch);
			
			$res = json_decode($res, true);
			if (!is_array($res)) return ['success' => false, 'result' => false];
			if (isset($res['error'])) return ['success' => false, 'result' => false];
			if (isset($res['matches']) && count($res['matches']) > 0) return ['success' => true, 'result' => false];
			return ['success' => true, 'result' => true];
			
		} catch(Exception $e) {
			return ['success' => false, 'result' => false];
		}
	}
}



