<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model
{
    protected $primaryKey = "user_id";

    protected $fillable = [
        "name",
        "surname",
        "email",
        "password",
        "role_id"
    ];

    protected $hidden = [
        "email_verified_at" => "datetime",
        "password" => "hashed",
        "delete_at" => "datetime"
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, "role_id", "role_id");
    }

    public function hasRole (string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }
}
