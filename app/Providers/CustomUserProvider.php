<?php

namespace App\Providers;

use App\User; 
use Illuminate\Auth\GenericUser; 
use Illuminate\Contracts\Auth\Authenticatable; 
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

class CustomUserProvider implements UserProvider {

	/**
	 * Retrieve a user by their unique identifier.
	 *
	 * @param  mixed $identifier
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveById($identifier)
	{
	    // TODO: Implement retrieveById() method.

	    $qry = User::select()->where('id', '=', $identifier)->first();

	    if($qry->count() > 0)
	    {
	        return $qry;
	    }
	    return null;
	}

	/**
	 * Retrieve a user by by their unique identifier and "remember me" token.
	 *
	 * @param  mixed $identifier
	 * @param  string $token
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByToken($identifier, $token)
	{
	    // TODO: Implement retrieveByToken() method.
	    $qry =  User::select()->where('id','=',$identifier)->where('remember_token','=',$token)->first();

	    if($qry->count() > 0)
	    {
	        return $qry;
	    }
	    return null;



	}

	/**
	 * Update the "remember me" token for the given user in storage.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param  string $token
	 * @return void
	 */
	public function updateRememberToken(Authenticatable $user, $token)
	{
	    // TODO: Implement updateRememberToken() method.
	    $user->setRememberToken($token);
	}

	/**
	 * Retrieve a user by the given credentials.
	 *
	 * @param  array $credentials
	 * @return \Illuminate\Contracts\Auth\Authenticatable|null
	 */
	public function retrieveByCredentials(array $credentials)
	{
	    // TODO: Implement retrieveByCredentials() method.
	    $qry = User::select()->where('email','=',$credentials['email'])->first();

	    if($qry->count() >0 )
	    {
	        return $qry;
	    }
	    return null;


	}

	/**
	 * Validate a user against the given credentials.
	 *
	 * @param  \Illuminate\Contracts\Auth\Authenticatable $user
	 * @param  array $credentials
	 * @return bool
	 */
	public function validateCredentials(Authenticatable $user, array $credentials)
	{
	    // TODO: Implement validateCredentials() method.
	    // we'll assume if a user was retrieved, it's good
	    if($user->isEmpty()) return false;
	    
	    if($user->email == $credentials['email'] && Hash::check($credentials['password'], $user->password))
	    {
	        return true;
	    }
	    return false;


	}
}