<?php
/** @noinspection PhpMissingFieldTypeInspection */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CompanyUser
 * @package App\Models
 * @property int $id
 * @property int $company_id
 * @property int $user_id
 * @property string $role
 */
class CompanyUser extends Model
{
    use HasFactory;

    protected $table = 'company_user';

    protected $guarded = [];
}
