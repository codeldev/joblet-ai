<?php

declare(strict_types=1);

namespace App\Http\Requests\Sitemap;

use App\Services\Sitemap\GeneratorService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

final class SitemapRequest extends FormRequest
{
    public function __invoke(): SymfonyResponse
    {
        return Response::make(
            content: (new GeneratorService)->get(),
            headers: ['Content-Type' => 'application/xml']
        );
    }

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [];
    }
}
