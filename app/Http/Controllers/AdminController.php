<?php

namespace App\Http\Controllers;

use Laravel\Sanctum\PersonalAccessToken;
use App\Mail\AccountActivationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tymon\JWTAuth\Providers\JWT\Provider;
use Tymon\JWTAuth\JWTGuard;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AdminController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
    if($validator->fails()){
        return response()->json($validator->errors()->toJson(),400);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)],
            ['rol' => 3]
        ));
        $token = JWTAuth::fromUser($user);
        log::info($token);

        $url = URL::temporarySignedRoute(
            'activate', now()->addMinutes(30), ['token' => $token]
        );

        Mail::to($user->email)->send(new AccountActivationMail($url));
        return response()->json([
            'message' => 'usuario registrado correctamente. verifica tu correo para activar tu cuenta ', 'user'=>$user
        ],201);
    }
    public function login(Request $request)
    {
        // Validar las credenciales
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
    
        // Si la validación falla, devolver los errores
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
    
        // Extraer las credenciales de la solicitud
        $user = User::where('email', $request->email)->first();
        log::info($user);

        // Intentar autenticar al usuario con las credenciales
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Credenciales no válidas'], 401);
        }
        if($user->rol ==1){
            return response()->json(['error' => 'Acceso negado, tienes que ser Administrador'], 401);
        }else{
            if (!$user->is_active) {
                $this->mandarcorreo($user);
                return response()->json([
                    'message' => 'Verifica tu correo para activar tu cuenta.'
                ],201);
            }
            
    
            $token = $user->createToken('access_token')->plainTextToken;
            
    
            $code = mt_rand(100000, 999999);
            $hashedCode = Hash::make($code);
            $expiresAt = now()->addMinutes(5); // Establece la expiración en 5 minutos
                // Almacenar el código en la base de datos
            VerificationCode::create([
                'user_id' => $user->id,
                'code' => $hashedCode,
                'expires_at' => $expiresAt, // Agrega la fecha de expiración
            ]);
    
            // Enviar el código por correo electrónico
            Mail::to($user->email)->send(new VerificationCodeMail($code));
            // Si la autenticación es exitosa, responder con el token
            return response()->json(['message' => 'Verifica tu correo electrónico para obtener el código de verificación.','token'=>$token ], 200);
        }
        }

}
