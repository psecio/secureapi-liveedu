<?php

namespace App\Middleware;
use \App\Model\ApiSession;
use \App\Model\User;
use \App\Model\ApiKey;

class SessionAuth
{
    use \App\Traits\JsonResponse;

    /**
     * Check the headers from the request to ensure they're for a valid session
     *
     * @param object $request Request instance
     * @param object $response Response instance
     * @return object Response
     */
    public function __invoke($request, $response, $next)
    {
        $token = $request->getHeader('X-Token');
        $hash = $request->getHeader('X-Token-Hash');

        // Verify the hash is a valid session
        $key = ApiKey::where(['key' => $token])->first();
        if ($key == null) {
            return $this->jsonError($response, 'Invalid session');
        }
        // Get the first session for the API key. Since we only should
        // have one active at a time, the first should be the only
        $session = $key->session;
        if ($session == null) {
            return $this->jsonError($response, 'Invalid session');
        }
        
        // Be sure it hasn't expired
        if (new \DateTime($session->expiration) <= new \DateTime()) {
            return $this->jsonError($response, 'Session expired');
        }

        // Check the status on the related user
        $user = $session->user;
        if ($user == null) {
            return $this->jsonError($response, 'Invalid session');
        }
        if ($user->status !== User::STATUS_ACTIVE) {
            return $this->jsonError($response, 'Account disabled');
        }

        // If we get here this means that the session validation is successful
        // and the user can continue on with their request
        $response = $next($request, $response);
        return $response;
    }
}
