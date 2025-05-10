<?php

declare(strict_types=1);

namespace App\Providers;

use App\Actions\Contact\SendContactAction;
use App\Actions\Contact\SendFeedbackAction;
use App\Actions\Orders\OrderAction;
use App\Actions\Orders\PaymentAction;
use App\Actions\Orders\ProcessAction;
use App\Actions\System\SendExceptionMailAction;
use App\Contracts\Actions\Contact\SendContactActionInterface;
use App\Contracts\Actions\Contact\SendFeedbackActionInterface;
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
    }
}
