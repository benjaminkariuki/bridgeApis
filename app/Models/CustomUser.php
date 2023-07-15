<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;


class CustomUser extends Model  implements Authenticatable
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'customusers';
    
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'contacts',
        'role_id',
        'password',
        'token',
        'profile_pic',
    ];

    protected $guarded = [
        'reset_password_token',
        'expiry_time'
];
    

    public function getAuthIdentifierName()
    {
        // Return the name of the identifier column (usually 'id')
        return 'id';
    }

    public function getAuthIdentifier()
    {
        // Return the value of the identifier column for the user
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        // Return the hashed password for the user
        return $this->password;
    }

     /**
     * Get the remember token for the user.
     *
     * @return string|null
     */
    public function getRememberToken()
    {
        return null;
    }

    /**
     * Set the remember token for the user.
     *
     * @param  string|null  $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Leave empty since you don't need to set the remember token
    }

    /**
     * Get the name of the "remember me" token column.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return '';
    }

    public function role()
    {
        return $this->belongsTo(Roles::class, 'role_id', 'id');
    }

}