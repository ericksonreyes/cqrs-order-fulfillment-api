<?php


namespace App\Http\Controllers;


use App\Http\Controllers\Exception\LoginFailedException;
use App\Models\Query\Employee;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{

    public function auth(Request $request)
    {
        try {

            $employee = Employee::where('username', $request->get('username'))
                ->where('password', md5($request->get('password')))
                ->first();

            if (!$employee) {
                throw new LoginFailedException('Incorrect username or password.');
            }

            $key = $this->container()->getParameter('jwt_secret_key');
            $host = $this->container()->getParameter('application_host');
            $algorithm = $this->container()->getParameter('jwt_algorithm');
            $token = array(
                "iss" => $host,
                "aud" => $host,
                "iat" => time(),
                "exp" => time() + 86400,
                "sub" => $employee->id
            );
            $token = JWT::encode($token, $key, $algorithm);

            $responseArray = [
                'accessToken' => $token
            ];

            return $this->response($responseArray, 200);
        } catch (Exception $exception) {
            return $this->exception($exception);
        }
    }

}