<?php

namespace Kingscode\ActiveCampaignApi\Log;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log as OriginalLog;
use Psr\Log\LoggerInterface;

class Log extends OriginalLog
{
    public static function activecampaign(): LoggerInterface
    {
        if (config()->has('logging.channels.activecampaign')
            && App::environment(['local', 'testing'])
        ) {
            return OriginalLog::channel('activecampaign');
        } else {
            return OriginalLog::channel();
        }
    }

}
