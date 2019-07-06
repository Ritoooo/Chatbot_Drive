<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class FirebaseController extends Controller
{

	public $database;

	public function __construct(){
		$serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/FirebaseKey.json');
		$firebase = (new Factory)
		    ->withServiceAccount($serviceAccount)
		    ->create();
		    
		$this->database = $firebase->getDatabase();
	}

    public function index(){
    	
		$ref = $this->database->getReference('Productos');
		//$key = $ref->push()->getKey();
		/*$ref->getChild($key)->set([
			'nombre' => 'Rack Acumulativo',
			'Tipo1' => true
		]);*/
		/*$ref->getChild($key)->update([
			'Li3teBmRxbTYQ92i0zr' => 3
			
		]);*/
		$snapshot = $ref->getSnapshot();
		$value = $snapshot->getValue();
		return $value;
	}

	public function getAll(){

	}
}
