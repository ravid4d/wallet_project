<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Str;

class MainController extends Controller
{
    // function for registering the user
    public function create(Request $req)
    {
        try
        {
        //   validating the coming request
            $validator = Validator::make($req->all(),[
                "name"=>"required",
                "email"=>"unique:users,email",
                "password"=>"required"
            ]);
            if($validator->fails())
            {
                // loging the error in log file
                Log::error('Registration Validation error', [
                    'errors' => $validator->errors(),
                ]);
                // returning the response 
                return response()->json([
                    'message' => 'Bad or invalid request',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $credential = $req->all();
            $credential['password'] =  bcrypt($credential['password']);

            // registering the user in database
            $user = User::create($credential);
            
            
            $success['name'] = $user->name;

            return response()->json([
                'message' => 'User Created Successfully',
                'data' =>$success ,
            ], 200);

        }
        catch(\Exception $ex)
        {
            Log::error('Registration Controller Error', ['error' => $ex->getMessage()]);

            
            return response()->json([
                'message' => 'request failed',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
             //   validating the coming request
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required', 
            ]);

            if ($validator->fails()) {
             // loging the error in log file
                Log::error('Login Validation error', [
                    'errors' => $validator->errors(),
                ]);
             // returning the response
                return response()->json([
                    'message' => 'Bad or invalid request',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $email = $request->email;
            $password = $request->password;

            // checking the login credential 
            if(Auth::attempt(['email' =>$email, 'password' => $password]))
            {
                
                $user = Auth::user();
                // generating the token for specific user
                $success["token"] = $user->createToken('remember_token')->plainTextToken;
                // return Str::random(64);
                $success['name'] = $user->name;

                

                return response()->json([
                    'message' => 'login successfully',
                    'data' =>$success ,
                ], 200);
            }
            else
            {
                return response()->json([
                    'message' => 'Credentials are wrong',
                    'data' =>null ,
                ], 400);
            }

        } catch (\Exception $ex) {
            
            Log::error('Login request failed', ['error' => $ex->getMessage()]);

            
            return response()->json([
                'message' => 'login request failed',
                'error' => $ex->getMessage(),
            ], 500);
        }
    }

    public function addWallet(Request $request)
    {
        try {
             //   validating the coming request
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:3|max:100',
            ]);

            if ($validator->fails()) {
             // loging the error in log file
             
                Log::error('Adding Money Validation error', [
                    'errors' => $validator->errors(),
                ]);

                return response()->json([
                    'message' => 'Bad or invalid request',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $user = $request->user();
            // updating the wallet column 
            $user->amount += $request->input('amount');
            $user->save();

            return response()->json([
                'message' => 'Money added to wallet successfully',
                'data' =>["Balance"=>number_format((float)$user->amount, 2, '.', '')],
            ], 200);

        } 
         catch (\Exception $ex) {
            Log::error('Error adding money to wallet', [
                'error' => $ex->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error adding money to wallet',
                'errors' => $ex->getMessage(),
            ], 500);
        }
    }

    function buycookies(Request $request)
    {
        try
        {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|numeric|min:1|max:10',
            ]);

            if ($validator->fails()) {
             
                Log::error('Buy Validation error', [
                    'errors' => $validator->errors(),
                ]);

                return response()->json([
                    'message' => 'Bad or invalid request',
                    'errors' => $validator->errors(),
                ], 400);
            }
            $user = $request->user();

            $price = ($request->input('quantity')*1);
            // checking the price is less then the wallet balance
            if($user->amount >= $price)
            {
            $user->amount  = ($user->amount - $price);
            $user->save();
            

            return response()->json([
                'message' => 'Cookie Successfully Bought',
                'data' => ["remaining_balance"=>number_format((float)$user->amount, 2, '.', '')],
            ], 200);
           }
           else
           {
            return response()->json([
                'message' => 'your wallet has not sufficient amount',
                'data' => ["remaining_balance"=>number_format((float)$user->amount, 2, '.', '')],
            ],400);
           }

           
        }
        catch(\Exception $ex)
        {
            Log::error('Error buying Cookie', [
                'error' => $ex->getMessage(),
            ]);

            return response()->json([
                'message' => 'Error buying cookie',
                'errors' => $ex->getMessage(),
            ], 500);
        }
    }
}
