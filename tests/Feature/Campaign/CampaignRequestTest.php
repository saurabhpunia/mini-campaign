<?php

use App\Http\Requests\CampaignRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use App\Traits\CsvTrait;

uses(RefreshDatabase::class);
uses(CsvTrait::class);

it('validates campaign creation request', function(){

    $arrRequestData = [
        'name' => 'Test Campaign',
        'file' => UploadedFile::fake()->create('test.csv', 100, 'text/csv'),
    ];

    //create a campaign request
    $objRequest = new CampaignRequest();

    //validate the request
    $objValidator = Validator::make($arrRequestData, $objRequest->rules());

    expect($objValidator->passes())->toBeTrue();

});

it('fails validation when name is missing', function(){

    $arrRequestData = [
        'file' => UploadedFile::fake()->create('test.csv', 100, 'text/csv'),
    ];

    //create a campaign request
    $objRequest = new CampaignRequest();

    //validate the request
    $objValidator = Validator::make($arrRequestData, $objRequest->rules());

    //fails if name not found in the request
    expect($objValidator->fails())->toBeTrue();

});

it('fails validation when file is missing', function(){

    $arrRequestData = [
        'name' => 'Test Campaign',
    ];

    //create a campaign request
    $objRequest = new CampaignRequest();

    //validate the request
    $objValidator = Validator::make($arrRequestData, $objRequest->rules());

    //fails if file is not present in the request
    expect($objValidator->fails())->toBeTrue();

});

it('fails validation when file is not csv', function(){

    $arrRequestData = [
        'file' => UploadedFile::fake()->create('test.pdf', 100, 'application/pdf'),
    ];

    //create a campaign request
    $objRequest = new CampaignRequest();

    $objValidator = Validator::make($arrRequestData, $objRequest->rules());

    //fails if file is not csv
    expect($objValidator->fails())->toBeTrue();

});

it('fails validation when contact email is not valid in csv file', function(){

    $arrRequestData = [
        'name' => 'Test',
        'file' => UploadedFile::fake()->createWithContent('test.csv', "name,email\nSaurabh,saurabhpunia.com\nsaurabh,saurabh@punia.com"),
    ];

    //fetch and format the csv data
    $arrData = $this->getDataFromCsvInFormat($arrRequestData['file'], ['name', 'email']);

    //removes header from data
    array_shift($arrData);

    //validate the csv fetched data
    $objValidator = Validator::make($arrData, [
        '*.name' => 'required',
        '*.email' => 'required|email',
    ]);

    //fails if email is not valid in data
    expect($objValidator->fails())->toBeTrue();

});

it('fails validation when contact name is empty in csv file', function(){

    $arrRequestData = [
        'name' => 'Test',
        'file' => UploadedFile::fake()->createWithContent('test.csv', "name,email\n,saurabhpunia@punia.com\nsaurabh,saurabh@punia.com"),
    ];

    //fetch and format the csv data
    $arrData = $this->getDataFromCsvInFormat($arrRequestData['file'], ['name', 'email']);

    //removes header from data
    array_shift($arrData);

    //validate the csv fetched contact data
    $objValidator = Validator::make($arrData, [
        '*.name' => 'required',
        '*.email' => 'required|email',
    ]);

    //fails if contact name is empty
    expect($objValidator->fails())->toBeTrue();

});

it('fails validation when contact email is empty in csv file', function(){

    $arrRequestData = [
        'name' => 'Test',
        'file' => UploadedFile::fake()->createWithContent('test.csv', "name,email\n,saurabhpunia@punia.com\nsaurabh,"),
    ];

    //fetch and format the csv data
    $arrData = $this->getDataFromCsvInFormat($arrRequestData['file'], ['name', 'email']);

    //removes header from data
    array_shift($arrData);

    //validate the csv fetched contact data
    $objValidator = Validator::make($arrData, [
        '*.name' => 'required',
        '*.email' => 'required|email',
    ]);

    //fails if contact email is empty
    expect($objValidator->fails())->toBeTrue();

});
