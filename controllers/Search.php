<?php

namespace controllers;

use classes\CURL;
use classes\Debug;
use classes\Message;
use classes\Router;
use classes\Rover;

class Search extends \classes\Controller
{
    public function show()
    {
        $this->userMustBeLogged();

        $data = array();
        if($this->isPOST()) {
            $data = $this->search();
        }

        $data['rovers']     = $this->rovers();
        $data['cameras']    = $this->cameras();

        $view = new \classes\View('layout_search');
        $view->load("search", $data);
    }


    /**
     * Get results from NASA API
     * @return array
     */
    private function search() {
        $rover_name     = filter_input(INPUT_POST, "rover_name");
        $rover_camera   = filter_input(INPUT_POST, "rover_camera");
        $rover_sol      = filter_input(INPUT_POST, "rover_sol");

        $url = "https://api.nasa.gov/mars-photos/api/v1/rovers/".$rover_name."/photos?sol=".$rover_sol."&api_key=".api_key;

        // Message::info($url);

        if($rover_camera != 'ALL')
            $url .= '&camera='.$rover_camera;

        $today = date('Y-m-d');

        // NASA API has limited query count per account so lets save search results in a file
        $cache_filename = $today.'_'.$rover_name.'_'.$rover_sol.'_'.$rover_camera;

        $data['results'] = CURL::getJSON($url, $cache_filename);
        $data['rover_name']     = $rover_name;
        $data['rover_camera']   = $rover_camera;
        $data['rover_sol']      = $rover_sol;

        return $data;
    }


    /**
     * Used only by jQuery AJAX method
     */
    public function manifest() {
        if(!isset($_REQUEST['rover'])) {
            exit();
        }

        $rover = new Rover($_REQUEST['rover']);

        header('Content-type: application/json');
        echo json_encode( $rover->manifest );
        exit();
    }


    /**
     * List of all rovers
     * @return array
     */
    private function rovers(): array
    {
        return [
            'Curiosity' => 'Curiosity',
            'Opportunity' => 'Opportunity',
            'Spirit' => 'Spirit'
        ];
    }

    /**
     * List of all cameras
     * @return string[]
     */
    private function cameras(): array
    {
        return ['ALL' => 'All cameras',
            'FHAZ' => 'Front Hazard Avoidance Camera',
            'RHAZ' => 'Rear Hazard Avoidance Camera',
            'MAST' => 'Mast Camera',
            'CHEMCAM' => 'Chemistry and Camera Complex',
            'MAHLI' => 'Mars Hand Lens Imager',
            'MARDI' => 'Mars Descent Imager',
            'NAVCAM' => 'Navigation Camera',
            'PANCAM' => 'Panoramic Camera',
            'MINITES' => 'Miniature Thermal Emission Spectrometer (Mini-TES)'];
    }
}

/*
 * Rovers: curiosity, opportunity,
 *
 *
 *
 *
 */