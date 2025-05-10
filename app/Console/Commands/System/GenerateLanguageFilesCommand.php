<?php

/** @noinspection MkdirRaceConditionInspection */

declare(strict_types=1);

namespace App\Console\Commands\System;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use JsonException;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name       : 'languages:generate',
    description: 'Generates application language json files.',
)]
final class GenerateLanguageFilesCommand extends Command
{
    public function handle(): int
    {
        return $this->runConsoleCommand();
    }

    private function runConsoleCommand(): int
    {
        try
        {
            $this->checkLanguageDirectoryExists();
            $this->checkTranslationsDirectoryExists();

            $this->getTranslations()->each(
                /**
                 * @param  array<string, mixed>  $data
                 *
                 * @throws JsonException
                 */
                callback: fn (array $data, string $lang) => $this->buildLanguageFile(lang: $lang, data: $data)
            );

            $this->info(string: 'Language files created!');

            return self::SUCCESS;
        }
        catch (JsonException | RuntimeException $e)
        {
            $this->error(string: $e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws JsonException
     */
    private function buildLanguageFile(string $lang, array $data): void
    {
        file_put_contents(
            filename : lang_path(path: "{$lang}.json"),
            data     : json_encode(
                value: $data,
                flags: JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE
            )
        );
    }

    /** @codeCoverageIgnore */
    private function checkLanguageDirectoryExists(): void
    {
        if (file_exists(filename: lang_path()))
        {
            return;
        }

        if (mkdir(directory: lang_path(), permissions: 0755, recursive: true))
        {
            return;
        }

        if (is_dir(filename: lang_path()))
        {
            return;
        }

        throw new RuntimeException(
            message: sprintf(format: 'Directory "%s" was not created', values: lang_path())
        );
    }

    private function checkTranslationsDirectoryExists(): void
    {
        if (! file_exists(filename: database_path(path: 'translations')))
        {
            throw new RuntimeException(
                message: 'Translations directory at database/translations does not exist'
            );
        }
    }

    /** @return Collection<string, array<string, mixed>>  */
    private function getTranslations(): Collection
    {
        /** @var array<int, string> $languages */
        $languages = File::directories(
            directory: database_path(path: 'translations')
        );

        return collect(value: $languages)->mapWithKeys(
            callback: fn (string $dir) => [basename(path: $dir) => $this->getLanguageFiles(directory: $dir)]
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function getLanguageFiles(string $directory): array
    {
        $files = collect(value: File::allFiles(directory: $directory))->mapWithKeys(
            callback: fn (SplFileInfo $file) => [$this->getGroup(file: $file) => require ($file->getPathname())]
        )->toArray();

        /** @var array<string, mixed> $dotArray */
        $dotArray = Arr::dot(array: $files);

        return $dotArray;
    }

    private function getGroup(SplFileInfo $file): string
    {
        return str(string: $file->getFilename())
            ->replace(search: '.' . $file->getExtension(), replace: '')
            ->toString();
    }
}
