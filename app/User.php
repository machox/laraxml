<?php

namespace App;

use Illuminate\Auth\Authenticatable; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use \App\Obay;

class User extends Obay implements AuthenticatableContract {

    use Authenticatable;

    /**
     * The file associated with the model
     *
     * @var string
     */
    protected $file = 'admin.xml';

    /**
     * Root element of the document
     *  
     * @var string
     */
    protected $root = 'data';  

    /**
     * Child elements of the root
     * 
     * @var string 
     */
    protected $child = 'row';         

    /**
     * Child elements of the child
     * 
     * @var string 
     */
    protected $child_end = 'field';     

    /**
     * The primary key for the model
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $mapping = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function getKey() {
        return (int)$this->id;
    }   

    public function getAuthPassword()
    {
        return $this->password;
    }

    public function setRememberToken($token) {
        $this->remember_token = $token;
        $this->save();
    }

    public static function create($data) {
        $qry = User::select()->orderBy('id', 'desc')->first();
        $id = 1;
        if($qry->count() > 0) $id = $qry->id + 1;
        $user = new User;
        $user->id = $id;
        $user->email = $data['email'];
        $user->username = $data['email'];
        $user->name = $data['name'];
        $user->password = $data['password'];
        $user->save();
    }
}
