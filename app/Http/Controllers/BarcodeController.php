<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class BarcodeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Log list
     *
     * @return response
     */
    public function index(Request $request)
    {
        
        if($request->filled('code')) {

            try {
                $renderer = new ImageRenderer(new RendererStyle(400), new SvgImageBackEnd());
                $writer = new Writer($renderer);
                return $writer->writeString($request->code);
            } catch (Exception $e) {
                return 'error';
            }
        }
        return 'No code';
    }

}
