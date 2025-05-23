<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Contact\SendContactAction;
use App\Actions\Contact\SendFeedbackAction;
use App\Actions\Generator\GenerateAction;
use App\Actions\Generator\UploadAction;
use App\Actions\Images\GenerateAction as ImageGenerateAction;
use App\Actions\Images\ResizeAction;
use App\Actions\Orders\OrderAction;
use App\Actions\Orders\PaymentAction;
use App\Actions\Orders\ProcessAction;
use App\Actions\System\SendExceptionMailAction;
use App\Contracts\Actions\Contact\SendContactActionInterface;
use App\Contracts\Actions\Contact\SendFeedbackActionInterface;
use App\Contracts\Actions\Generator\GenerateActionInterface;
use App\Contracts\Actions\Generator\UploadActionInterface;
use App\Contracts\Actions\Images\GenerateActionInterface as ImageGenerateActionInterface;
use App\Contracts\Actions\Images\ResizeActionInterface;
use App\Contracts\Actions\Orders\OrderActionInterface;
use App\Contracts\Actions\Orders\PaymentActionInterface;
use App\Contracts\Actions\Orders\ProcessActionInterface;
use App\Contracts\Actions\System\SendExceptionMailActionInterface;
use Illuminate\Support\ServiceProvider;
use Override;

final class ActionServiceProvider extends ServiceProvider
{
    #[Override]
    public function register(): void
    {
        $this->app->bind(
            abstract: OrderActionInterface::class,
            concrete: OrderAction::class
        );

        $this->app->bind(
            abstract: PaymentActionInterface::class,
            concrete: PaymentAction::class
        );

        $this->app->bind(
            abstract: ProcessActionInterface::class,
            concrete: ProcessAction::class
        );

        $this->app->bind(
            abstract: SendContactActionInterface::class,
            concrete: SendContactAction::class
        );

        $this->app->bind(
            abstract: SendFeedbackActionInterface::class,
            concrete: SendFeedbackAction::class
        );

        $this->app->bind(
            abstract: SendExceptionMailActionInterface::class,
            concrete: SendExceptionMailAction::class
        );

        $this->app->bind(
            abstract: GenerateActionInterface::class,
            concrete: GenerateAction::class
        );

        $this->app->bind(
            abstract: UploadActionInterface::class,
            concrete: UploadAction::class
        );

        $this->app->bind(
            abstract: ImageGenerateActionInterface::class,
            concrete: ImageGenerateAction::class
        );

        $this->app->bind(
            abstract: ResizeActionInterface::class,
            concrete: ResizeAction::class
        );
    }
}
