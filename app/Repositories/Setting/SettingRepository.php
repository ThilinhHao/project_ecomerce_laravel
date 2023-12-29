<?php

namespace App\Repositories\Setting;

use App\Models\Settings;
use App\Repositories\Base\BaseRepository;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Settings $settings)
    {
        parent::__construct($settings);
    }
}
