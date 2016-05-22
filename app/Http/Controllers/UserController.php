<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use \Validator;

class UserController extends Controller {

    /** Method to create a new user */
    public function registerUser(Request $request) {
        /** get data from JSON */
        $data = $request->json()->all();

        /** Validate the data */
        $validator = Validator::make($data, User::rules());

        /** Check if passes */
        if ($validator->fails()) {
            /** Back to user the error */
            return response()->json(["error" => $validator->messages()], 400);
        }

        /** Get input data */
        $name = $data["name"];
        $email = $data["email"];
        $password = $data["password"];

        /** Save the user */
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);

        $user->save();

        /** Return the user */
        return $user->toJson();
    }

    /** Method to login a user */
    public function loginUser(Request $request) {
        /** Get JSON from Payload */
        $data = $request->json()->all();

        /** Login the user */
        if (Auth::once($data)) {
            /** If is authenticated, create the token */
            /** Get the data */
            $now = Carbon::now();

            /** Get the user */
            $user = Auth::user();

            /** Add the token and save it */
            $user->api_token = md5($user->email . $user->password . $now->timestamp);
            $user->save();

            /** Return the user token and validation */
            return $user->toJson();
        } else {
            return response()->json(["error" => "E-mail or password incorrect"], 401);
        }

    }
}
