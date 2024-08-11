<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\Campaign;
use App\Models\User;
use App\Mail\CampaignMail;
use App\Mail\AcknowledgementMail;

class CampaignJob implements ShouldQueue
{
    use Queueable;

    public $arrContacts;
    public $intCampaignId;
    /**
     * Create a new job instance.
     */
    public function __construct($intCampaignId, $arrContacts)
    {
        $this->intCampaignId = $intCampaignId;
        $this->arrContacts = $arrContacts;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $intSentMails = 0;

        foreach($this->arrContacts as $arrContact){
            //try sending campaign mail to contacts
            try{
                Mail::to($arrContact['email'])->send(new CampaignMail($arrContact['name']));
                $intSentMails++;
            }
            //log the error if fails to send mail
            catch(\Exception $e){
                \Log::info('Error while sending mail to: '.$arrContact['email'].' ('.$e->getMessage().')');
            }
        }

        //update the sent mail data to particular campaign
        $objCampaign = Campaign::find($this->intCampaignId);
        $objCampaign->processed_contacts += $intSentMails;
        $objCampaign->save();

        //send an acknowledgement mail to user if all contacts are processed
        if($objCampaign->total_contacts === $objCampaign->processed_contacts){
            $objUser = User::find($objCampaign->user_id);
            //try sending an acknowledgement to user
            try{
                Mail::to($objUser->email)->send(new AcknowledgementMail($objCampaign, $objUser));
            }
            //log error if fails to send
            catch(\Exception $e){
                \Log::info('Error while sending mail to user: '.$objUser->email.' ('.$e->getMessage().')');
            }
        }

    }
}
