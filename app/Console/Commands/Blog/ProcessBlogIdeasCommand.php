<?php

declare(strict_types=1);

namespace App\Console\Commands\Blog;

use App\Contracts\Console\Commands\Blog\ProcessBlogIdeasCommandInterface;
use App\Enums\StorageDiskEnum;
use App\Models\BlogIdea;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(
    name        : 'blog:ideas:process',
    description : 'Process blog idea JSON files and store them in the database'
)]
final class ProcessBlogIdeasCommand extends Command implements ProcessBlogIdeasCommandInterface
{
    private bool $showOutput = false;

    private readonly Filesystem $ideasDisk;

    private readonly Filesystem $failedDisk;

    /** @var array<int, string> */
    private array $requiredKeys = [];

    /**
     * @var array<string, string>
     */
    private array $messages = [];

    public function __construct()
    {
        $this->ideasDisk  = StorageDiskEnum::BLOG_IDEAS->disk();
        $this->failedDisk = StorageDiskEnum::BLOG_UNPROCESSABLE->disk();

        $this->setRequiredKeys();
        $this->setMessages();

        parent::__construct();
    }

    public function handle(): int
    {
        $this->showOutput = (bool) $this->option(key: 'debug');

        $this->displayMessage(message: $this->messages['processing']);

        $jsonFiles = $this->getJsonFiles();

        if ($jsonFiles === [])
        {
            $this->displayMessage(message: $this->messages['noFiles']);

            return self::SUCCESS;
        }

        $this->displayMessage(message: trans(
            key    : $this->messages['numFiles'],
            replace: ['count' => count(value: $jsonFiles)]
        ));

        collect(value: $jsonFiles)
            ->each(callback: fn (string $file) => $this->processFile(file: $file));

        $this->displayMessage(message: $this->messages['complete']);

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption(
            name       : 'debug',
            description: 'Show debug output'
        );
    }

    private function processFile(string $file): void
    {
        $this->displayMessage(message: trans(
            key    : $this->messages['fileProcessing'],
            replace: ['file' => $file]
        ));

        try
        {
            /** @var array<string, mixed> $data */
            $data = $this->getIdeaData(file: $file);

            if (! $this->validateRequiredKeys(data: $data))
            {
                $this->unprocessableFile(file: $file);

                return;
            }

            $this->storeIdeaData(data: $data);

            $this->ideasDisk->delete(paths: $file);

            $this->displayMessage(message: trans(
                key    : $this->messages['fileSuccess'],
                replace: ['file' => $file]
            ));
        }
        catch (JsonException $e)
        {
            $this->displayMessage(message: trans(
                key    : $this->messages['jsonError'],
                replace: ['file' => $file, 'error' => $e->getMessage()]
            ), type : 'error');

            $this->moveToUnprocessable(file: $file);
        }
        catch (Throwable $e)
        {
            $this->displayMessage(message: trans(
                key    : $this->messages['fileError'],
                replace: ['file' => $file, 'error' => $e->getMessage()]
            ), type : 'error');

            $this->moveToUnprocessable(file: $file);
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JsonException
     */
    private function getIdeaData(string $file): array
    {
        /** @var array<string, mixed> $data */
        $data = json_decode(
            json       : (string) $this->ideasDisk->get($file),
            associative: true,
            depth      : 256,
            flags      : JSON_THROW_ON_ERROR
        );

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @throws Throwable
     */
    private function storeIdeaData(array $data): void
    {
        DB::transaction(callback: function () use ($data): void
        {
            BlogIdea::create(attributes: [
                'topic'         => $data['topic'],
                'keywords'      => $data['keywords'],
                'focus'         => $data['focus'],
                'requirements'  => $data['requirements'],
                'additional'    => $data['additional'],
                'schedule_date' => $this->getNextScheduleDate(),
            ]);
        });
    }

    private function unprocessableFile(string $file): void
    {
        $this->displayMessage(message: trans(
            key    : $this->messages['unprocessable'],
            replace: ['file' => $file]
        ), type: 'error');

        $this->moveToUnprocessable(file: $file);
    }

    /**
     * @return array<int, string>
     */
    private function getJsonFiles(): array
    {
        /** @var array<int, string> $files */
        $files = array_filter(
            array   : $this->ideasDisk->files(),
            callback: static fn (string $file): bool => Str::endsWith(haystack: $file, needles: '.json')
        );

        return $files;
    }

    /** @param array<string, mixed> $data */
    private function validateRequiredKeys(array $data): bool
    {
        foreach ($this->requiredKeys as $key)
        {
            if (! array_key_exists(key: $key, array: $data))
            {
                $this->displayMessage(message: trans(
                    key    : $this->messages['missingKey'],
                    replace: ['key' => $key]
                ), type: 'error');

                return false;
            }

            if (! notEmpty(value: $data[$key]))
            {
                $this->displayMessage(message: trans(
                    key    : $this->messages['missingValue'],
                    replace: ['key' => $key]
                ), type: 'error');

                return false;
            }
        }

        return true;
    }

    private function moveToUnprocessable(string $file): void
    {
        $this->failedDisk->put(
            path    : $file,
            contents: $this->ideasDisk->get($file) ?? ''
        );

        $this->ideasDisk->delete(paths: $file);
    }

    private function getNextScheduleDate(): Carbon
    {
        /** @var string|null $lastDate */
        $lastDate = BlogIdea::max(column: 'schedule_date');

        if ($lastDate !== null)
        {
            return Carbon::parse(time: $lastDate)
                ->addDays(value: $this->getDaysBetween());
        }

        return Carbon::tomorrow();
    }

    private function getDaysBetween(): int
    {
        $defaultDays = 2;
        $daysBetween = config(
            key    : 'blog.post.schedule',
            default: $defaultDays
        );

        return is_numeric(value: $daysBetween)
            ? (int) $daysBetween
            : $defaultDays;
    }

    private function displayMessage(string $message, string $type = 'info'): void
    {
        if ($type === 'error' && app()->isProduction())
        {
            Log::error(message: $message);
        }

        if ($this->showOutput)
        {
            match ($type)
            {
                'info'  => $this->info(string: $message),
                'error' => $this->error(string: $message),
                default => $this->line(string: $message),
            };
        }
    }

    private function setRequiredKeys(): void
    {
        $this->requiredKeys = [
            'topic',
            'keywords',
            'focus',
            'requirements',
            'additional',
        ];
    }

    private function setMessages(): void
    {
        $this->messages = [
            'processing'     => trans(key: 'blog.command.idea.process.processing'),
            'noFiles'        => trans(key: 'blog.command.idea.process.no.files'),
            'numFiles'       => trans(key: 'blog.command.idea.process.num.files'),
            'fileProcessing' => trans(key: 'blog.command.idea.process.file.processing'),
            'fileSuccess'    => trans(key: 'blog.command.idea.process.file.success'),
            'fileError'      => trans(key: 'blog.command.idea.process.file.error'),
            'jsonError'      => trans(key: 'blog.command.idea.process.json.error'),
            'unprocessable'  => trans(key: 'blog.command.idea.process.unprocessable'),
            'complete'       => trans(key: 'blog.command.idea.process.complete'),
            'missingKey'     => trans(key: 'blog.command.idea.process.missing.key'),
            'missingValue'   => trans(key: 'blog.command.idea.process.missing.value'),
            'invalidArray'   => trans(key: 'blog.command.idea.process.invalid.array'),
        ];
    }
}
