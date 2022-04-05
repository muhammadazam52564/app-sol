<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\follow;
use App\Mail\AccountVerificationEMail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Mail\ForgotPassword;
use Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller{
// admin 0
// recruiters 1
// player 2

    public function signup(Request $request){
        try{
              $validator = \Validator::make($request->all(), [
                'name'      => 'bail|required|max:255',
                'type'      => 'required',
                'email'     => 'required|unique:users',
                'password'  => 'required|min:6|max:30',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                $user = new User;
                $user->name = $request->name;
                $user->type = $request->type;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);
                // $user->otp = rand(1000, 9999);
                $user->otp = 1234;
                if ( $user->save()) {
                    return response()->json([
                        'status' => true,
                        'message' => 'Sign Up Successfully',
                        'data' =>  $user->makeHidden(['verified_at', 'updated_at', 'created_at']),
                    ]  , 200);
                }
            }
        }catch(\Exception $e){

            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'data' => 0,
            ], 400);
        }
    }

    public function VerfiyOtp(Request $request){

        try{
              $validator = \Validator::make($request->all(), [
                'email'     => 'bail|required|max:255',
                'otp'       => 'required|min:4|max:6',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      =>0,
                ], 400);
            }else{
                if (User::where('email', $request->email)->where('otp', $request->otp)->count() >0){
                        $user = User::where('email', $request->email)->first();
                        // $user->otp = rand(1000, 9999);
                        $user->otp = 1234;
                        $user->status = 1;
                        $user->save();
                        return response()->json([
                            'status' => true,
                            'message' => 'OTP verified Successfully ',
                            'data' => $user->makeHidden(['created_at', 'updated_at', 'verified_at', 'token', 'email_verified_at', 'otp']),
                        ], 200);
                }else{
                    return response()->json([
                        'status' => false,
                        'error' => 'you entered incorrect OTP',
                        'data' => 0,
                    ], 400);
                }


            }
        } catch(\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => 0,
            ], 400);
        }
    }

    public function login(Request $request){
        try{
              $validator = \Validator::make($request->all(), [
                'email' => 'bail|required',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                 if (auth()->attempt(['email' => $request->email, 'password' => $request->password])){
                    $user = auth()->user();
                    $followers = follow::where('follow_to', auth::id())->count();
                    $user->followers =  $followers;
                    $following = follow::where('follow_by', auth::id())->count();
                    $user->following =  $following;
                    return response()->json([
                       'status'     => true,
                        'message'   => 'Successfully LogIn',
                        'data'      =>  $user->makeHidden(['created_at', 'updated_at', 'token', 'otp', 'email_verified_at']),
                    ], 200);
                 }else{
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Log In Failed',
                        'data'      => 0
                        ], 400);
                 }
            }
        } catch(\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => 0,
            ], 400);
        }
    }

    public function update_profile_image(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'user_id'   => 'bail|required',
                'image'     => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                $user = User::find($request->user_id);
                if(empty($user)){
                    return response()->json([
                        'status' => false,
                        'message' => 'User Does Not Exists!',
                        'data' => 0,
                    ], 400);
                }else{
                    $base64_image = $request->image;
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $data = base64_decode($data);
                        $img = preg_replace('/^data:image\/\w+;base64,/', '', $base64_image);
                        $type = explode(';', $base64_image)[0];
                        $type = explode('/', $type)[1]; // png or jpg etc
                        if($type == 'png' || $type == 'PNG' || $type == 'jpg' || $type == 'JPG' || $type == 'jpeg' || $type == 'JPEG'){
                            $imageName = Str::random(10).'.'.$type;
                            \Storage::disk('profile_images')->put($imageName, $data);
                            // this disk is defined in config/filesystems.php under Disks section
                            $img_path = 'profile_images/'.$imageName;
                        }else{
                            return response()->json([
                                'status' => false,
                                'message' => 'Please Choose a Valid Image!',
                                'data' => 0,
                            ], 400);
                        }
                    }
                    $user->profile_image = $img_path;
                    if($user->save()){
                        $user = User::find($request->user_id);
                        return response()->json([
                            'status' => true,
                            'message' => 'Profile Image Updated Successfully!',
                            'data' => $user->makeHidden(['created_at', 'updated_at', 'email_verified_at', 'verification_code', 'self_description', 'type', 'token', 'otp']),
                        ], 200);
                    }
                }
            }
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => 0,
            ], 200);
        }
    }

    public function update_profile_image_by_parts(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'user_id'   => 'bail|required',
                'image'     => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      =>0,
                ], 400);
            }else{
                $user = User::find($request->user_id);
                if(empty($user)){
                    return response()->json([
                        'status' => false,
                        'message' => 'User Does Not Exists!',
                        'data' => 0,
                    ], 400);
                }else{
                    $newfilename = time() .'.'. $request->image->getClientOriginalExtension();
                    $request->file('image')->move(public_path("profile_images"), $newfilename);
                    $user->profile_image = 'profile_images/'.$newfilename;
                    if($user->save()){
                        $user = User::find($request->user_id);
                        return response()->json([
                            'status' => true,
                            'message' => 'Profile Image Updated Successfully!',
                            'data' => $user->makeHidden(['created_at', 'updated_at', 'email_verified_at', 'verification_code', 'self_description', 'type', 'token', 'otp']),
                        ], 200);
                    }
                }
            }
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => 400,
                'error' => $e->getMessage(),
                'data' => 0,
            ], 200);
        }
    }

    public function update_club_logo(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'user_id'   => 'bail|required',
                'image'     => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      =>0,
                ], 400);
            }else{
                $user = User::find($request->user_id);
                if(empty($user)){
                    return response()->json([
                        'status' => 400,
                        'error' => 'User Does Not Exists!',
                        'data' => 0,
                    ], 200);
                }else{
                    $base64_image = $request->image;
                    // return preg_match('/^data:image\/(\w+);base64,/', $base64_image);
                    if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                        $data = substr($base64_image, strpos($base64_image, ',') + 1);
                        $data = base64_decode($data);
                        $img = preg_replace('/^data:image\/\w+;base64,/', '', $base64_image);
                        $type = explode(';', $base64_image)[0];
                        $type = explode('/', $type)[1]; // png or jpg etc
                        if($type == 'png' || $type == 'PNG' || $type == 'jpg' || $type == 'JPG' || $type == 'jpeg' || $type == 'JPEG'){
                            $imageName = Str::random(10).'.'.$type;
                            \Storage::disk('club_logos')->put($imageName, $data);
                            // this disk is defined in config/filesystems.php under Disks section
                            $img_path = 'club_logos/'.$imageName;
                        }else{
                            return response()->json([
                                'status' => false,
                                'message' => 'Please Choose a Valid Image!',
                                'data' => 0,
                            ], 200);
                        }
                    }
                    $user->club_logo = $img_path;
                    if($user->save()){
                        $user = User::find($request->user_id);
                        return response()->json([
                            'status' => false,
                            'message' => 'Logo Image Updated Successfully!',
                            'data' => $user->makeHidden(['created_at', 'updated_at', 'email_verified_at', 'verification_code', 'self_description', 'type', 'token', 'otp']),
                        ], 200);
                    }
                }
            }
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => 400,
                'error' => $e->getMessage(),
                'data' => 0,
            ], 200);
        }
    }

    public function update_club_logo_by_parts(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'user_id'   => 'bail|required',
                'image'     => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      =>0,
                ], 400);
            }else{
                $user = User::find($request->user_id);
                if(empty($user)){
                    return response()->json([
                        'status' => false,
                        'error' => 'User Does Not Exists!',
                        'data' => 0,
                    ], 400);
                }else{
                    $type = $request->image->getClientOriginalExtension();
                    if($type == 'png' || $type == 'PNG'
                    || $type == 'jpg' || $type == 'JPG'
                    || $type == 'jpeg' || $type == 'JPEG')
                    {
                        $newfilename = time() .'.'. $type;
                        $request->file('image')->move(public_path("club_logos"), $newfilename);
                        // \Storage::disk('club_logos')->put($imageName, $data);
                        $img_path = 'club_logos/'.$newfilename;

                    }else{
                            return response()->json([
                                'status' => false,
                                'message' => 'Please Choose a Valid Image!',
                                'data' => 0,
                            ], 400);
                        }
                    $user->club_logo = $img_path;
                    if($user->save()){
                        $user = User::find($request->user_id);
                        return response()->json([
                            'status' => false,
                            'message' => 'Logo Image Updated Successfully!',
                            'data' => $user->makeHidden(['created_at', 'updated_at', 'email_verified_at', 'verification_code', 'self_description', 'type', 'token', 'otp']),
                        ], 200);
                    }
                }
            }
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'data' => 0,
            ], 200);
        }
    }

    public function update_profile(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                if (empty(User::find($request->user_id))) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Could Not Complete Your Acction',
                        'error'     => 'User Not Exists',
                    ], 400);
                }
                $user = User::find($request->user_id);
                if ($request->has('email')){

                    $email = User::where('id', $request->user_id)->pluck('email');

                    if ($email[0] != $request->email){

                        if (User::where('id', '!=', $request->user_id)->where('email', $request->email)->count() == 0) {
                            $user->email = $request->email;
                        }else{
                            return response()->json([
                                'status'    => false,
                                'error'     => 'Email Already Taken',
                                'data'      => 0,
                            ], 200);
                        }
                    }
                }
                if ($request->has('name')) {
                    $user->name = $request->name;
                }
                if ($request->has('gender')) {
                    $user->gender = $request->gender;
                }
                if ($request->has('age')) {
                    $user->age = $request->age;
                }
                if ($request->has('height')) {
                    $user->height = $request->height;
                }
                if ($request->has('position')) {
                    $user->position = $request->position;
                }
                if ($request->has('country')) {
                    $user->country = $request->country;
                }
                if ($request->has('country_code')) {
                    $user->country_logo = $request->country_code;
                }
                if ($request->has('club')) {
                    $user->club = $request->club;
                }
                if ($request->has('bio')) {
                    $user->bio = $request->bio;
                }
                if( $user->save()) {
                    $usr = User::find($request->user_id);
                    $followers = follow::where('follow_to', $usr->id)->count();
                    if($followers  == 0) {
                        $usr->followers =  0;
                    }else{
                        $usr->followers =  $followers;
                    }

                    $following = follow::where('follow_by',  $usr->id)->count();
                    if($followers  == 0) {
                        $usr->following =  0;
                    }else{
                        $usr->following =  $following;
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Profile Updated Successfully ',
                        'data' =>  $usr->makeHidden(['verified_at', 'updated_at', 'created_at', 'email', 'email_verified_at', 'otp', 'status', 'token', 'type']),
                    ]  , 200);
                }
            }
        }catch(\Exception $e){

            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => $validator->errors()->first(),
            ], 400);
        }
    }

    public function get_profile(Request $request){
        try{
            $user = User::find($request->id);
            $followers = follow::where('follow_to', $user->id)->count();
                    if($followers  == 0) {
                        $user->followers =  0;
                    }else{
                        $user->followers =  $followers;
                    }

                    $following = follow::where('follow_by',  $user->id)->count();
                    if($followers  == 0) {
                        $user->following =  0;
                    }else{
                        $user->following =  $following;
                    }
            return response()->json([
                'status' => true,
                'message' => 'User Data ',
                'data' =>  $user->makeHidden(['coach_type', 'verified_at', 'updated_at', 'created_at', 'email', 'email_verified_at', 'otp', 'status', 'token', 'type']),
            ], 200);
        }catch(\Exception $e){

            return response()->json([
                'status'    => false,
                'error'     => $e->getMessage(),
                'data'      => 0
            ], 400);
        }
    }

    public function update_profile_recruter(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }else{
                if (empty(User::find($request->user_id))) {
                    return response()->json([
                        'status'    => false,
                        'message'   => 'Could Not Complete Your Acction',
                        'error'     => 'User Not Exists',
                    ], 400);
                }
                $user = User::find($request->user_id);
                if ($request->has('name')) {
                    $user->name = $request->name;
                }
                if ($request->has('coach_type')) {
                    $user->coach_type = $request->coach_type;
                }
                if ($request->has('country')) {
                    $user->country = $request->country;
                }
                if ($request->has('club')) {
                    $user->club = $request->club;
                }
                if ($request->has('bio')) {
                    $user->bio = $request->bio;
                }
                if( $user->save()) {
                    $usr = User::find($request->user_id);
                    $followers = follow::where('follow_to', $usr->id)->count();
                    if($followers  == 0) {
                        $usr->followers =  0;
                    }else{
                        $usr->followers =  $followers;
                    }
                    $following = follow::where('follow_by',  $usr->id)->count();
                    if($followers  == 0) {
                        $usr->following =  0;
                    }else{
                        $usr->following =  $following;
                    }
                    return response()->json([
                        'status' => true,
                        'message' => 'Profile Updated Successfully ',
                        'data' =>  $usr->makeHidden(['verified_at', 'updated_at', 'created_at']),
                    ]  , 200);
                }
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'error' => $validator->errors()->first(),
            ], 400);
        }
    }

    public function forgot_password(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'email' => 'bail|required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }
            $user = User::where('email', $request->email)->first();
            if(empty($user))
            {
                return response()->json([
                    'status' => 400,
                    'message' => 'User does not exists!',
                    'data' => 0,
                ], 200);
            }
            $code = rand(1111, 9999);
            $user->otp = $code;
            $user->save();
            $data = [
                "opt"=> $code,
            ];
            // \Mail::to($request->email)->send(new ForgotPassword($code));
                return response()->json([
                    'status' => 200,
                    'message' => 'A Verification Code has been Sent to your Email!',
                    'data' => $data,
                ], 200);
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => 0,
            ], 200);
        }
    }

    public function verify_code(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'email' => 'bail|required',
                'otp' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }
            $user = User::where('email', $request->email)->first();
            if($request->otp == $user->otp)
            {
                $user->email_verified_at = Carbon::now();
                $user->otp = 0;
                $user->save();
                return response()->json([
                    'status'    => true,
                    'message'   => 'Verified Successfully!',
                    'data'      => $user->makeHidden(['created_at', 'updated_at', 'email_verified_at', 'verification_code', 'cover_image', 'self_description', 'opening_time', 'type', 'token', 'otp'])
                ], 200);
            }else{
                return response()->json([
                    'status' => false,
                    'error' => 'Invalid Verification Code!',
                    'data' => 0,
                ], 200);
            }
        }catch(\Exception $e)
        {
            if($request->expectsJson())
            {
                return response()->json([
                    'status' => 400,
                    'error' => $e->getMessage(),
                    'data' => 0,
                ], 400);
            }
        }
    }

    public function set_password(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'email'     => 'bail|required',
                'password'  => 'required|min:6',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      =>0,
                ], 400);
            }
            $user = User::where('email', $request->email)->first();
            if(empty($user)){
                return response()->json([
                    'status' => false,
                    'message' => 'User does not exists!',
                    'data' => 0,
                ], 200);
            }
            $user->password = bcrypt($request->password);
            if($user->save()){
                return response()->json([
                    'status' => true,
                    'message' => 'Password Changed Successfully!',
                    'data' => $user->makeHidden(['created_at', 'updated_at', 'email_verified_at', 'verification_code', 'cover_image', 'self_description', 'opening_time', 'type', 'token', 'otp']),
                ], 200);
            }
        }catch(\Exception $e)
        {
            if($request->expectsJson)
            {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'data' => 0,
                ], 200);
            }
        }
    }

    public function change_password(Request $request){
        try{
             $validator = \Validator::make($request->all(), [
                'user_id' => 'bail|required',
                'old_password' => 'required',
                'password' => 'required|min:6',

            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => 0,
                ], 400);
            }
            $user = User::find($request->user_id);
            if(empty($user))
            {
                return response()->json([
                    'status'    => false,
                    'error'     => 'User not Found',
                    'data'      => 0,

                ], 200);
            }

            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'status' =>  false,
                    'error' => 'incorrect your old paasord',
                    'data' => 0,
                ], 200);
            }
            $user->password = bcrypt($request->password);
            if($user->save()){
                return response()->json([
                    'status' => true,
                    'message' => 'Password Changed Successfully!',
                    'data' => $user->makeHidden(['created_at', 'updated_at', 'verification_code', 'type', 'token']),
                ], 200);
            }
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => 0,
            ], 200);
        }
    }

    public function signout(Request $request){
        try{
            $validator = \Validator::make($request->all(), [
                'user_id' => 'bail|required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      =>0,
                ], 400);
            }
            $user = User::find($request->user_id);
            if(empty($user)){

                return response()->json([
                    'status' => false,
                    'message' => 'User does not exists!',
                    'data' => 0,
                ], 200);
            }
            $user->token = 0;
            if($user->save())
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Logged Out Successfullty !',
                    'data' => 0,
                ], 200);
            }
        }catch(\Exception $e){
            if($request->expectsJson())
            {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'data' => 0,
                ], 200);
            }
        }
    }

}
