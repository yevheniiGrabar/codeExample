<?php


namespace App\Enums\Permissions;


use App\Enums\AbstractEnum;

class PermissionsGroupEnum extends AbstractEnum
{
    public const PERMISSIONS_SETTINGS = 'Permissions Settings';
    public const USER_LIST = 'User list';
    public const CALENDARS = 'Calendars';
    public const EVENTS = 'Events';
    public const COMPANY = 'Company';
}
