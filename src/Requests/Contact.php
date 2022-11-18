<?php

namespace Kingscode\ActiveCampaignApi\Requests;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Validator;

class Contact extends AbstractRequest
{

    public function all(): array
    {
        return $this->activeCampaign->get('contacts');
    }

    public function find(int $id): array
    {
        return $this->activeCampaign->get('contacts/' . $id);
    }

    public function findByEmail(string $email): array
    {
        return $this->activeCampaign->get('contacts', ['email' => $email]);
    }

    public function create(array $data): array
    {
        $validator = $this->validateData($data);

        return $this->activeCampaign->post('contacts', ['contact' => $validator->validated()]);
    }

    public function update(int $id, array $data): array
    {
        $validator = $this->validateData($data);

        return $this->activeCampaign->put('contacts/' . $id, ['contact' => $validator->validated()]);
    }

    public function delete(int $id): array
    {
        return $this->activeCampaign->delete('contacts/' . $id);
    }

    private function validateData(array $data)
    {
        return Validator::make($data, [
            'email'               => 'required',
            'firstName'           => 'sometimes|string',
            'lastName'            => 'sometimes|string',
            'phone'               => 'sometimes|string',
            'fieldValues'         => 'array|sometimes',
            'fieldValues.*.field' => 'sometimes|numeric',
            'fieldValues.*.value' => 'sometimes',
        ]);
    }

}
