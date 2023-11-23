<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Partner
 * @package App\Models
 * @property int $id
 * @property string $company_name
 * @property string $email
 * @property string $phone
 * @property string $contact_person
 * @property string $ip_address
 */
class Partner extends Model
{
    use HasFactory;

    protected $guarded = [];
}
