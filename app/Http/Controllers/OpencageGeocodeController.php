<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use OpenCage\Geocoder\Geocoder;

class OpencageGeocodeController extends Controller
{

    private $opencageApiKey;
    private $geocoder;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->opencageApiKey = env('OPENCAGE_API_KEY');
        $this->geocoder = new Geocoder($this->opencageApiKey);
    }

    public function setGeocoder($key="")
    {
        $this->geocoder = new Geocoder($key);
    }

    public function setOpencageApiKey($key="")
    {
        $this->opencageApiKey = $key;
    }

    public function getAddress(Request $request)
    {
        try {
            $location = $request->input('loc');
            if (empty($location))
                throw new Exception("Empty Location");

            $dateNow = Carbon::now();
            $result = $this->geocoder->geocode($location); # latitude,longitude (y,x)
            // $data = $result;
            $data = [
                'address' => $result['results'][0]['formatted'],
                'lat' => $result['results'][0]['geometry']['lat'],
                'lng' => $result['results'][0]['geometry']['lng'],
                'datetime' => $dateNow,
            ];

            return response()->json([
                'status' => 200,
                'message' => 'success',
                'data' => $data
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
                'data' => ''
            ]);
        }
    }
}
