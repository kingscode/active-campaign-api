<?php

namespace Kingscode\ActiveCampaignApi\Requests;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class CustomFields extends AbstractRequest
{

    public function all(): array
    {
        return $this->activeCampaign->get('fields');
    }

    public function find(int $id): array
    {
        return $this->activeCampaign->get('fields/' . $id);
    }

    public function create(array $data): array
    {
        $validator = $this->validateData($data);

        return $this->activeCampaign->post('fields', ['field' => $validator->validated()]);
    }

    public function update(int $id, array $data): array
    {
        $validator = $this->validateData($data);

        return $this->activeCampaign->put('fields/' . $id, ['field' => $validator->validated()]);
    }

    public function delete(int $id): array
    {
        return $this->activeCampaign->delete('fields/' . $id);
    }

    private function validateData(array $data)
    {
        return Validator::make($data, [
            'type'       => 'required|string',
            'title'      => 'required|string',
            'isrequired' => 'sometimes|boolean',
            'descript'   => 'sometimes|string',
            'perstag'    => 'sometimes|string',
            'defval'     => 'sometimes|string',
            'visible'    => 'sometimes|boolean',
            'ordernum'   => 'sometimes|numeric',
        ]);
    }

}
