<?php

namespace Kingscode\ActiveCampaignApi\Requests;

use Illuminate\Support\Facades\Validator;

class Tags extends AbstractRequest
{

    public function all(): array
    {
        return $this->activeCampaign->get('tags');
    }

    public function addToContact(array $data): array
    {
        $validator = $this->validateContactTagsData($data);

        return $this->activeCampaign->post('contactTags', ['contactTag' => $validator->validated()]);
    }

    public function create(array $data): array
    {
        $validator = $this->validateData($data);

        return $this->activeCampaign->post('tags', ['tag' => $validator->validated()]);
    }

    public function update(int $id, array $data): array
    {
        $validator = $this->validateData($data);

        return $this->activeCampaign->put('tags/' . $id, ['tag' => $validator->validated()]);
    }

    public function delete(int $id): array
    {
        return $this->activeCampaign->delete('tags/' . $id);
    }

    private function validateData(array $data)
    {
        return Validator::make($data, [
            'tag'         => 'required|string',
            'tagType'     => 'required|string',
            'description' => 'sometimes|string',
        ]);
    }

    private function validateContactTagsData(array $data)
    {
        return Validator::make($data, [
            'contact' => 'required|numeric',
            'tag'     => 'required|numeric',
        ]);
    }

}
