<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User as User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;

class UserController extends Controller
{
    protected $payload = [];

    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->payload = [
            'status' => false,
            'data' => null,
            'err' => [
                'code' => 1,
                'message' => 'Unauthorized'
            ]
        ];
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $payload = $this->payload;
        $credentials = $request->only(['email', 'password']);

        $niceNames = array(
            'email' => 'E-Posta',
            'password' => 'Şifre',
        );
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        $validator->setAttributeNames($niceNames);

        try {

            if ($validator->fails()){
                throw new Exception('1');
            }
            if (!$token = JWTAuth::attempt($credentials)) {
                throw new Exception('invalid_credentials');
            }
            $payload = [
                'status' => true,
                'data' => [
                    '_token' => $token
                ],
                'err' => null
            ];

            if (empty(auth()->user()->email_verified_at)) {
                $payload['err'] = [
                    'message' => 'Doğrulanmamış e-Mail',
                    'code' => 2
                ];
            }
            $statusCode = 200;
        } catch (Exception $e) {
            $payload['err']['message'] = $e->getMessage();
            if ($e->getMessage()=="1"){
                $payload['err']['message'] = $validator->errors();
            }
            $statusCode = 401;
        } catch (JWTException $e) {
            $payload['err']['message'] = 'Token Oluşturulamadı';
            $statusCode = 500;
        }
        return response()->json($payload, $statusCode);
    }

    /**
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $payload = $this->payload;
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6',
            'username' => 'required|unique:users|alpha_dash',
            'name' => 'required',
            'phone' => 'required|unique:users',
        ]);
        $niceNames = array(
            'email' => 'E-Posta',
            'password' => 'Şifre',
            'username' => 'Kullanıcı Adı',
            'name' => 'İsim ve Soyisim',
            'phone' => 'Telefon Numarası',
        );
        $validator->setAttributeNames($niceNames);

        try {
            if ($validator->fails()){
                throw new Exception('1');
            }
            $user = User::create([
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'password' => Hash::make($request->post('password')),
                'username' => $request->post('username'),
                'phone' => $request->post('phone'),
            ]);
            $payload = [
                'status' => true,
                'data' => [
                    'User' => $user
                ],
                'err' => null
            ];
            $statusCode = 200;
        } catch (Exception $e) {

            $payload['err']['message'] = $e->getMessage();
            if ($e->getMessage() == "1") {
                $payload['err']['message'] = $validator->errors();
            }
            $statusCode = 401;

        }
        return response()->json($payload, $statusCode);
    }

    /**
     * @return JsonResponse
     */
    public function detail(): JsonResponse
    {
        $this->payload = [
            'status' => true,
            'data' => [
                'User' => auth()->user()
            ],
            'err' => null
        ];
        return response()->json($this->payload);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function emailVerify(Request $request)
    {
        $payload = $this->payload;
        $niceNames = array(
            'verifyCode' => 'Doğrulama Kodu',
        );
        $validator = Validator::make($request->all(), [
            'verifyCode' => 'required',
        ]);
        $validator->setAttributeNames($niceNames);

        try {
            if ($validator->fails()) {
                throw new Exception('1');
            }
            $user = auth()->user();
            if ($request->verifyCode == $user->verify_code) {
                $user->email_verified_at = now();
                $user->save();

                $payload['data']['user'] = $user;
                $payload['status'] = true;
                $payload['err'] = null;
            } else {
                $payload['err'] = [
                    'code' => 1,
                    'message' => 'Yanlış Doğrulama Kodu'
                ];
            }
            $statusCode = 200;
        } catch (Exception $e) {
            $payload['err']['message'] = $e->getMessage();
            if ($e->getMessage() == "1") {
                $payload['err']['message'] = $validator->errors();
            }
            $statusCode = 401;
        } catch (JWTException $e) {
            $payload['err']['message'] = 'Token Oluşturulamadı';
            $statusCode = 500;
        }
        return response()->json($payload, $statusCode);
    }

    public function resetPassword(Request $request)
    {
        $payload = $this->payload;
        $niceNames = array(
            'verifyCode' => 'Doğrulama Kodu',
            'password' => 'Şifre'
        );
        $validator = Validator::make($request->all(), [
            'verifyCode' => 'required',
            'password' => 'required|min:6'
        ]);
        $validator->setAttributeNames($niceNames);

        try {
            if ($validator->fails()) {
                throw new Exception('1');
            }
            $user = auth()->user();
            if ($request->verifyCode == $user->verify_code) {
                $user->password = Hash::make($request->password);
                $user->save();

                $payload['data']['user'] = $user;
                $payload['status'] = true;
                $payload['err'] = null;
            } else {
                $payload['err'] = [
                    'code' => 1,
                    'message' => 'Yanlış Doğrulama Kodu'
                ];
            }
            $statusCode = 200;
        } catch (Exception $e) {
            $payload['err']['message'] = $e->getMessage();
            if ($e->getMessage() == "1") {
                $payload['err']['message'] = $validator->errors();
            }
            $statusCode = 401;
        } catch (JWTException $e) {
            $payload['err']['message'] = 'Token Oluşturulamadı';
            $statusCode = 500;
        }
        return response()->json($payload, $statusCode);
    }

    public function changePassword(Request $request)
    {
        $payload = $this->payload;
        $niceNames = array(
            'password' => 'Şifre'
        );
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6'
        ]);
        $validator->setAttributeNames($niceNames);

        try {
            if ($validator->fails()) {
                throw new Exception('1');
            }
            $user = auth()->user();
            $user->password = Hash::make($request->password);
            $user->save();

            $payload['data']['user'] = $user;
            $payload['status'] = true;
            $payload['err'] = null;
            $statusCode = 200;
        } catch (Exception $e) {
            $payload['err']['message'] = $e->getMessage();
            if ($e->getMessage() == "1") {
                $payload['err']['message'] = $validator->errors();
            }
            $statusCode = 401;
        } catch (JWTException $e) {
            $payload['err']['message'] = 'Token Oluşturulamadı';
            $statusCode = 500;
        }
        return response()->json($payload, $statusCode);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createVerifyCode(Request $request)
    {
        $verifyCode = Str::random(6);
        $user = auth()->user();
        $user->verify_code = $verifyCode;
        $user->save();
        $this->payload = [
            'status' => true,
            'data' => [
                'verifyCode' => $verifyCode
            ],
            'err' => null
        ];
        return response()->json($this->payload);
    }

    /**
     * Kullanıcının çıkış işlemini yap ve token'ı kullanılamaz duruma getir.
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        auth()->logout();
        $data = [
            'status' => true,
            'data' => [
                'message' => 'Çıkış Yapıldı'
            ],
            'err' => null
        ];
        return response()->json($data, 200);
    }

    /**
     * Son kullanma tarihi geçmiş olan JWT nin tekrar kullanılır hale gelmesi
     * için yenileme işlemi.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $data = [
            'status' => true,
            'data' => [
                '_token' => auth()->refresh()
            ],
            'err' => null
        ];
        return response()->json($data, $this->payload['code']);
    }

}
