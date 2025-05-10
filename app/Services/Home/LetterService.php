<?php

declare(strict_types=1);

namespace App\Services\Home;

use App\Enums\DateFormatEnum;
use Illuminate\Support\Collection;
use Random\RandomException;

final class LetterService
{
    /** @var array<string, array<int, string>> */
    private array $fakeData = [];

    /** @var Collection<int, string> */
    private Collection $formats;

    /** @return Collection<int, array<string, mixed>> */
    public function get(): Collection
    {
        $this->setFakeData();
        $this->setDateFormats();

        return collect(value: generateRandomNumbers())
            ->map(callback: fn (int $number, int $index): array => $this->buildItem($number, $index + 1));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RandomException
     */
    private function buildItem(int $number, int $index): array
    {
        $replace = $this->buildFakeData(index: $index);

        return [
            'document' => $this->buildDocumentName(index: $index),
            'lines'    => [
                trans(key: "home.preview.example.letter.{$number}.line1", replace: $replace),
                trans(key: "home.preview.example.letter.{$number}.line2", replace: $replace),
                trans(key: "home.preview.example.letter.{$number}.line3"),
            ],
        ];
    }

    /**
     * @return array<string, string>
     *
     * @throws RandomException
     */
    private function buildFakeData(int $index): array
    {
        return [
            'name'     => $this->fakeData['names'][$index],
            'job'      => $this->fakeData['jobs'][$index],
            'company'  => $this->fakeData['companies'][$index],
            'date'     => $this->getRandomDate(),
        ];
    }

    /** @throws RandomException */
    private function buildDocumentName(int $index): string
    {
        $fileDate = now()->copy()->addSeconds(value: random_int(1, 30));
        $fileName = str(string: $this->fakeData['documents'][$index])
            ->lower()
            ->toString();

        return trans(
            key    : 'home.hero.preview.doc',
            replace: ['file' => $fileName, 'date' => $fileDate->timestamp]
        );
    }

    /** @throws RandomException */
    private function getRandomDate(): string
    {
        /** @var int $format */
        $format = $this->formats->keys()->random();

        $dateFormat = DateFormatEnum::from(value: $format)
            ->format();

        return getRandomFutureDate()
            ->format(format: $dateFormat);
    }

    private function setFakeData(): void
    {
        $this->fakeData = [
            'names'     => getFakeNames(take: 3),
            'jobs'      => getFakeJobs(take: 3),
            'companies' => getFakeCompanies(take: 3),
            'documents' => getFakeDocuments(take: 3),
        ];
    }

    private function setDateFormats(): void
    {
        /** @var Collection<int, string> $formats */
        $formats = collect(value: DateFormatEnum::getFormats());

        $this->formats = $formats;
    }
}
