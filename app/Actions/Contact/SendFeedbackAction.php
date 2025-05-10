<?php

declare(strict_types=1);

namespace App\Actions\Contact;

use App\Contracts\Actions\Contact\SendFeedbackActionInterface;
use App\Enums\MessageTypeEnum;
use App\Models\Message;
use App\Notifications\Contact\FeedbackMessageNotification;
use App\Services\Models\UserService;
use Illuminate\Support\Facades\DB;
use Throwable;

final class SendFeedbackAction implements SendFeedbackActionInterface
{
    /** @param array<string,string> $validated */
    public function handle(array $validated, callable $success, callable $failed): void
    {
        try
        {
            $this->storeMessage(validated: $validated);
            $this->sendEmail(validated: $validated);

            $success();
        }
        catch (Throwable $th)
        {
            report(exception: $th);

            $failed();
        }
    }

    /**
     * @param  array<string,string>  $validated
     *
     * @throws Throwable
     */
    private function storeMessage(array $validated): void
    {
        DB::transaction(callback: static function () use ($validated): void
        {
            /** @var string|null $userId */
            $userId = auth()->id();

            $validated['type']    = MessageTypeEnum::FEEDBACK;
            $validated['user_id'] = $userId;

            Message::create(attributes: $validated);
        });
    }

    /** @param array<string,string> $validated */
    private function sendEmail(array $validated): void
    {
        UserService::getSupportUser()->notify(
            instance: new FeedbackMessageNotification(feedbackData: $validated)
        );
    }
}
