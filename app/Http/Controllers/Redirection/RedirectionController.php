<?php

namespace App\Http\Controllers\Redirection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedirectionController extends Controller {
	
	public function redirectUser(Request $request, $hash) {
		
		$url = DB::table('urls')->where('hash', $hash)->select('long_url')->get();
		
		if (isset($url) && count($url) > 0) {
			return redirect($url[0]->long_url);
		} else {
			return redirect('/');
		}
	}
	
}



