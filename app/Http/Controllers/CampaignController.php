<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Campaign;
use App\Http\Requests\CampaignRequest;
use Illuminate\Support\Facades\Validator;
use App\Jobs\CampaignJob;
use App\Traits\CsvTrait;

class CampaignController extends Controller
{
    use CsvTrait;

    /**
     * function name: index
     * function: render the campaign listing page
     * @param Illuminate\Http\Request
     * @return Inertia\Response
     */
    public function index(Request $objRequest): Response
    {
        return Inertia::render('Campaign/List');
    }

    /**
     * function name: getData
     * function: used to get the campaign listing data
     * @param Illuminate\Http\Request
     * @return $arrData
     */
    public function getData(Request $objRequest)
    {
        $arrData    =   Campaign::where('user_id', $objRequest->user()->id)->orderBy('created_at', 'DESC')
                            ->get(['name', 'file_path', 'total_contacts', 'processed_contacts', 'created_at']);

        return response()->json($arrData);
    }
    
    /**
     * function name: create
     * function: used to render the add campaign page
     * @param Illuminate\Http\Request
     * @return Inertia\Response
     */
    public function create(Request $objRequest): Response
    {
        return Inertia::render('Campaign/Add');
    }
    
    /**
     * function name: store
     * function: used to process the campaign data
     * @param App\Http\Requests\CampaignRequest
     * @return Inertia\Response | Illuminate\Http\RedirectResponse
     */
    public function store(CampaignRequest $objRequest)
    {

        //fetch and format the csv data
        $arrData = $this->getDataFromCsvInFormat($objRequest->file, ['name', 'email']);

        //check if csv file have data or not
        if(count($arrData) > 0){
            //if header is not proper show an error to user
            if($arrData[0]['name'] !== 'name' || $arrData[0]['email'] !== 'email'){
                return Inertia::render('Campaign/Add', [
                    'arrError' => ['File doesn\'t have proper headers.']
                ]);
            }
        }
        //if there is no data in csv file render page with validation
        else{
            return Inertia::render('Campaign/Add', [
                'arrError' => ['File doesn\'t have any row. Please upload file with data.',]
            ]);
        }

        //remove header while processing the data
        array_shift($arrData);

        //validate the csv data
        $validator = Validator::make($arrData, [
            '*.name' => 'required',
            '*.email' => 'required|email',
        ]);

        //if validation fails show errors to user
        if($validator->fails()) {
            return Inertia::render('Campaign/Add', [
                'arrError' => $validator->errors()->all(),
            ]);
        }
        else{
            //upload the csv file to storage
            $strFilePath    =   $objRequest->file('file')->store('campaign');
            //store the campaign data in db
            $intCampaignId  =   Campaign::insertGetId([
                'name'      =>  $objRequest->name,
                'file_path' =>  $strFilePath,
                'user_id'   =>  $objRequest->user()->id,
                'total_contacts' => count($arrData),
            ]);

            //divide the data into chunks for sending mails
            //sends 50 mails per minute
            $arrChunks      =   array_chunk($arrData, 50);

            foreach($arrChunks as $intChunkKey => $arrChunk){
                CampaignJob::dispatch($intCampaignId, $arrChunk)->delay(now()->addMinute($intChunkKey * 1));
            }

            //redirect to listing page for campaign listing
            return Redirect::route('campaign.list');
        }

    }

}
