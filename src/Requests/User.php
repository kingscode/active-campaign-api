<?php

namespace Kingscode\ActiveCampaignApi\Requests;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Validator;
use Kingscode\ActiveCampaignApi\Value\User as UserValue;

class User extends AbstractRequest
{

    public function me(): array
    {
        return $this->activeCampaign->get('users/me');
    }

    public function all(): array
    {
        return $this->activeCampaign->get('users');
    }

    public function find(int $id): array
    {
        return $this->activeCampaign->get('users/' . $id);
    }

    public function findByEmail(string $email): array
    {
        return $this->activeCampaign->get('users/email/' . $email);
    }

    public function findByUserName(string $username): array
    {
        return $this->activeCampaign->get('users/username/' . $username);
    }

    public function create(array $data): array
    {
        $validator = $this->validateData($data);

        return $this->activeCampaign->post('users', $validator->validated());
    }

    public function update(int $id, array $data): array
    {
        $validator = $this->validateData($data);

        return $this->activeCampaign->put('users/' . $id, $validator->validated());
    }

    private function validateData(array $data)
    {
        return Validator::make($data, [
            'username'  => 'required',
            'email'     => 'required',
            'firstName' => 'sometimes|string',
            'lastName'  => 'sometimes|string',
            'password'  => 'sometimes|string',
            'group'     => 'sometimes|numeric',
        ]);
    }

    public function delete(int $id): array
    {
        return $this->activeCampaign->delete('users/' . $id);
    }

}
