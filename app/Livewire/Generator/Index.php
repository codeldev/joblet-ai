<?php

declare(strict_types=1);

namespace App\Livewire\Generator;

use App\Actions\Generator\GenerateAction;
use App\Concerns\HasNotificationsTrait;
use App\Enums\DateFormatEnum;
use App\Enums\LanguageEnum;
use App\Enums\LetterCreativityEnum;
use App\Enums\LetterLengthEnum;
use App\Enums\LetterToneEnum;
use App\Livewire\Dashboard\Letter;
use App\Livewire\Forms\Generator\GeneratorForm;
use App\Models\Generated;
use App\Models\User;
use App\Services\Models\UserService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

final class Index extends Component
{
    use HasNotificationsTrait;

    public GeneratorForm $form;

    public bool $generating = false;

    public function mount(): void
    {
        if (Auth::check())
        {
            $this->fillFromSession();
            $this->setNameIfNotFilled();
        }
    }

    #[Computed]
    #[On('generator-credits-check')]
    public function creditsRequired(): bool
    {
        return Auth::check() && UserService::getRemainingCredits() < 1;
    }

    /** @return array<int, string> */
    #[Computed]
    public function dateFormats(): array
    {
        return DateFormatEnum::getFormats();
    }

    /** @return array<int, string> */
    #[Computed]
    public function languages(): array
    {
        return LanguageEnum::getLanguages();
    }

    /** @return array<int,array{label:string,description:string}> */
    #[Computed]
    public function creativityOptions(): array
    {
        return LetterCreativityEnum::getOptions();
    }

    /** @return array<int,array{label:string,description:string}> */
    #[Computed]
    public function toneOptions(): array
    {
        return LetterToneEnum::getOptions();
    }

    /** @return array<int,array{label:string,description:string}> */
    #[Computed]
    public function lengthOptions(): array
    {
        return LetterLengthEnum::getOptions();
    }

    public function render(): View
    {
        return view(view: 'livewire.generator.index')
            ->title(title: trans(key: 'generator.title'))
            ->layoutData(data: [
                'description' => trans(key: 'generator.description'),
            ]);
    }

    public function submit(): void
    {
        if (! $this->authCheck())
        {
            return;
        }

        if (! $this->hasSufficientCredits())
        {
            return;
        }

        if (! $this->hasUploadedResumeData())
        {
            return;
        }

        $this->generate();
    }

    public function generate(): void
    {
        $this->form->clearValidation();

        /** @var array<string, mixed> $settings */
        $settings = $this->form->validate();

        Session::put('letter', $settings);

        $this->generating = true;

        (new GenerateAction)->handle(
            settings: $settings,
            success : fn (Generated $generated) => $this->letterGenerationSuccess(
                generated: $generated
            ),
            failed  : fn (string $message) => $this->letterGenerationFailed(
                message: $message
            )
        );
    }

    public function clearForm(): void
    {
        $this->form->reset();
        $this->form->clearValidation();

        Session::forget(keys: 'letter');

        $this->setNameIfNotFilled();
    }

    private function fillFromSession(): void
    {
        if (Session::exists(key: 'letter'))
        {
            $this->form->fill(
                values: Session::get(key: 'letter')
            );
        }
    }

    private function setNameIfNotFilled(): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user instanceof User && ! notEmpty(value: $this->form->name))
        {
            $this->form->name = $user->name;

            Session::put(
                key  : 'letter.name',
                value: $this->form->name
            );
        }
    }

    private function authCheck(): bool
    {
        if (! Auth::check())
        {
            /** @var object $modal */
            $modal = Flux::modal(name: 'auth-required');

            if (method_exists(object_or_class: $modal, method: 'show'))
            {
                $modal->show();
            }

            return false;
        }

        return true;
    }

    private function hasSufficientCredits(): bool
    {
        if (UserService::getRemainingCredits() < 1)
        {
            $this->notifyError(
                message: trans(key: 'generator.insufficient.credits')
            );

            return false;
        }

        return true;
    }

    private function letterGenerationFailed(string $message): void
    {
        $this->generating = false;

        $this->notifyError(message: $message);
    }

    private function letterGenerationSuccess(Generated $generated): void
    {
        $this->generating = false;

        $this->dispatch(event: 'reload-credits-panel');
        $this->dispatch(event: 'generator-credits-check');

        /** @var object $dispatch */
        $dispatch = $this->dispatch(event: 'view-generated-letter', generatedId: $generated->id);

        if (method_exists(object_or_class: $dispatch, method: 'to'))
        {
            $dispatch->to(Letter::class);
        }
    }

    private function hasUploadedResumeData(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user instanceof User && notEmpty(value: $user->cv_content))
        {
            return true;
        }

        $this->notifyError(
            message: trans(key: 'generator.form.resume.error.missing')
        );

        return false;
    }
}
