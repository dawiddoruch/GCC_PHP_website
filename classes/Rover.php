<?php

namespace classes;

class Rover
{
    private string $rover_name;
    public array $manifest;

    public function __construct($rover_name) {
        $this->rover_name = $rover_name;
        $this->updateRover();
        $this->loadRover();
    }

    private function loadRover() {
        $this->manifest = DB::getOne('rover', ['name' => $this->rover_name]);
    }

    private function updateRover(): bool
    {
        $url = "https://api.nasa.gov/mars-photos/api/v1/manifests/".$this->rover_name."?api_key=".api_key;
        $today = date('Y-m-d');

        $record = DB::getOne('rover', ['name' => $this->rover_name]);

        // Add rover to our database if record not found
        if(!$record) {
            $data = CURL::getJSON($url);
            if(!$data)
                return false;

            if(!property_exists($data, 'photo_manifest'))
                return false;

            // Debug::print_r($data);            exit();

            $insertData = [
                'name'           => $data->photo_manifest->name,
                'landing_date'   => $data->photo_manifest->landing_date,
                'launch_date'    => $data->photo_manifest->launch_date,
                'status'         => $data->photo_manifest->status,
                'max_sol'        => $data->photo_manifest->max_sol,
                'max_date'       => $data->photo_manifest->max_date,
                'total_photos'   => $data->photo_manifest->total_photos,
                'last_updated'   => $today
                ];

            if(!DB::insert('rover', $insertData))
                return false;
        }
        else {
            // no need to update if mission is completed
            if($record['status'] == 'complete')
                return true;

            if($record['last_updated'] < $today) {
                $data = CURL::getJSON($url);
                if(!$data)
                    return false;

                $updateData = [
                    'status'         => $data->photo_manifest->status,
                    'max_sol'        => $data->photo_manifest->max_sol,
                    'max_date'       => $data->photo_manifest->max_date,
                    'total_photos'   => $data->photo_manifest->total_photos,
                    'last_updated'   => $today
                ];

                $result = DB::update('rover', $updateData, ['rover_id' => $record['rover_id']]);
                if(!$result)
                    return false;
            }
        }

        return true;
    }




}