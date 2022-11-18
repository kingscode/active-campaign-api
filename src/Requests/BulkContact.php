<?php

namespace Kingscode\ActiveCampaignApi\Requests;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class BulkContact extends AbstractRequest
{

    public function import(array $data): array
    {
        $validator = $this->validateData($data);

        $validatedData = $validator->validated();
        //There is a limit of 400 kb for an bulk import.
        $devision = intval(mb_strlen(serialize($validatedData), '8bit') / 399900) + 1; //399900

        if ($devision > 1) {
            $callBackArray['callback'] = data_get($validatedData, 'callback', []);
            $count = count($validatedData['contacts']);
            $chunkedData = array_chunk(data_get($validatedData, 'contacts', []), intval($count / $devision));
            $result = null;
            foreach ($chunkedData as $index => $validateDataChunk) {
                $callBackArray = data_set($callBackArray, 'callback.params.99', [
                    'key'   => 'chuncked parts of ' . count($chunkedData) - 1,
                    'value' => $index,
                ]);

                $validateDataChunk = array_merge($callBackArray, ['contacts' => $validateDataChunk]);
                $result = $this->activeCampaign->post('import/bulk_import', $validateDataChunk);
            }

            return $result;
        } else {
            return $this->activeCampaign->post('import/bulk_import', $validatedData);
        }
    }

    private function validateData(array $data)
    {
        return Validator::make($data, [
            'contacts.*.email'          => 'required',
            'contacts.*.first_name'     => 'sometimes|string',
            'contacts.*.last_name'      => 'sometimes|string',
            'contacts.*.phone'          => 'sometimes|string',
            'contacts.*.tags'           => 'array|sometimes',
            'contacts.*.fields'         => 'array|sometimes',
            'contacts.*.fields.*.id'    => 'sometimes|numeric',
            'contacts.*.fields.*.value' => 'sometimes',
            'callback'                  => 'array|sometimes',
        ]);
    }

}
