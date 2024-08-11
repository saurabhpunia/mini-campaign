<?php

use App\Jobs\CampaignJob;
use App\Mail\CampaignMail;
use App\Mail\AcknowledgementMail;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

uses(RefreshDatabase::class);

//test the campaign job and mail functionality
it('sends campaign emails and sends acknowledgement email upon completion', function () {
    // Fake the mail sending
    Mail::fake();

    // Create a user and campaign for the test
    $objUser = User::factory()->create(['email' => 'saurabh@gmail.com']);
    $campaign = Campaign::factory()->create([
        'user_id' => $objUser->id,
        'total_contacts' => 3, // Assume we have 3 contacts to process
        'processed_contacts' => 0,
    ]);

    // Define contacts to be used in the job
    $arrContacts = [
        ['name' => 'Saurabh', 'email' => 'saurabh@test.com'],
        ['name' => 'Punia', 'email' => 'punia@gmail.com'],
        ['name' => 'Test', 'email' => 'test@gmail.com'],
    ];

    // Dispatch the job
    dispatch(new CampaignJob($campaign->id, $arrContacts));

    // Assert that emails were sent
    foreach ($arrContacts as $arrContact) {
        Mail::assertSent(CampaignMail::class, function ($mail) use ($arrContact) {
            return $mail->hasTo($arrContact['email']);
        });
    }

    // Assert that an acknowledgment email was sent
    Mail::assertSent(AcknowledgementMail::class, function ($mail) use ($campaign, $objUser) {
        return $mail->hasTo($objUser->email) &&
               $mail->campaign->id === $campaign->id &&
               $mail->user->id === $objUser->id;
    });

    // Assert that the campaign's processed_contacts were updated
    $campaign->refresh();
    expect($campaign->processed_contacts)->toBe(count($arrContacts));
});
