<?php

namespace App\Controller;
use \App\Model\User;
use \App\Model\ApiSession;
use \App\Model\ApiKey;

class UserController extends \App\Controller\BaseController
{
    public function login($request, $response)
    {
        $v = \Psecio\Validation\Validator::getInstance();
        $data = $request->getParams();
        $rules = [
            'key' => 'required',
            'password' => 'required'
        ];
        $result = $v->execute($data, $rules);
        if ($result == false) {
            return $this->jsonError($response, 'Error', $v->errorArray());
        }

        $key = ApiKey::where(['key' => $data['key']])->first();
        if ($key == null) {
            return $this->jsonError($response, 'Invalid credentials');
        }

        // Find the user by username
        $user = $key->user;
        if ($user == false) {
            return $this->jsonError($response, 'Invalid credentials');
        }

        if (!password_verify($data['password'], $user->password)) {
            return $this->jsonError($response, 'Invalid credentials');
        }

        // We only want one session to be active at a time, so remove old ones
        $sessions = ApiSession::where(['user_id' => $user->id])->get();
        foreach ($sessions as $session) {
            $session->delete();
        }

        // When they log in, provide them with a random session ID
        // This will be stored in the DB and checked on following requests
        $sessionId = hash('sha512', random_bytes(256));
        ApiSession::create([
            'user_id' => $user->id,
            'session_id' => $sessionId,
            'key_id' => $key->id,
            'expiration' => date('Y-m-d H:i:s', strtotime(ApiSession::TIMEOUT))
        ]);
        $data = [
            'session' => $sessionId
        ];

        // Log our successful login - don't log credential information!
        // Internal identifiers mean you have to look it up but it also means
        // less to an attacker if they were to get your logs directly.
        $this->log->error('Login success', ['user_id' => $user->id]);

        return $this->jsonSuccess($response, 'Success!', $data);
    }

    public function register($request, $response)
    {
        $v = \Psecio\Validation\Validator::getInstance();
        $data = $request->getParams();
        $rules = [
            'email' => 'required|email',
            'username' => 'required',
            'name' => 'required',
            'password' => 'required'
        ];
        $result = $v->execute($data, $rules);
        if ($result == false) {
            return $this->jsonError($response, 'Error', $v->errorArray());
        }

        // Be sure the user doesn't already exist
        $find = User::where(['username' => $request->getParam('username')])->first();
        if ($find !== null) {
            return $this->jsonError($response, 'Invalid username');
        }

        $user = User::create([
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'email' => $data['email'],
            'name' => $data['name'],
            'status' => User::STATUS_ACTIVE,
            'password_reset_date' => strtotime('Y-m-d H:i:s')
        ]);
        return $this->jsonSuccess($response, 'User created succesfully!');
    }
}
