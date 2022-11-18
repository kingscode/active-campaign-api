<?php

namespace Kingscode\ActiveCampaignApi\Requests;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Kingscode\ActiveCampaignApi\Client\ActiveCampaign;
use Kingscode\ActiveCampaignApi\Value\AbstractValueObject;
use Kingscode\ActiveCampaignApi\Value\Contact as ContactValue;
use Kingscode\ActiveCampaignApi\Value\User as UserValue;

class AbstractRequest
{
    public function __construct(
        protected readonly ActiveCampaign $activeCampaign
    ) {
    }


    public function byLink(string $link): ?Response
    {
       return $this->activeCampaign->get($link);
    }

}
