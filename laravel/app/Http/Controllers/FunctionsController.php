<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class FunctionsController extends Controller {
  public static function RandomString($length) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
  }
}