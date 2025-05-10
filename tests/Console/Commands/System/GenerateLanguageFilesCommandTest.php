<?php

/** @noinspection PhpUnhandledExceptionInspection */
/** @noinspection StaticClosureCanBeUsedInspection */

declare(strict_types=1);

beforeEach(function (): void
{
    $this->commandSuccess   = 0;
    $this->commandFailure   = 1;
});

it(description: 'fails when translations directory does not exist', closure: function (): void
{
    $this->app->useDatabasePath(path: storage_path(path: 'testing/database'));

    $this->artisan(command: 'languages:generate')
        ->expectsOutput(output: 'Translations directory at database/translations does not exist')
        ->assertExitCode(exitCode: $this->commandFailure);
});

it(description: 'creates language files successfully', closure: function (): void
{
    $language = 'test';
    $dbPath   = storage_path(path: 'testing/database');
    $jsonPath = lang_path(path: "{$language}.json");

    $this->app->useDatabasePath(path: $dbPath);

    File::makeDirectory(
        path     : "$dbPath/translations/{$language}",
        mode     : 0777,
        recursive: true,
        force    : true
    );

    File::put(
        path    : "{$dbPath}/translations/{$language}/auth.php",
        contents: "<?php return ['login' => 'Login', 'register' => 'Register'];"
    );

    File::put(
        path    : "{$dbPath}/translations/{$language}/validation.php",
        contents: "<?php return ['required' => 'The :attribute field is required.'];"
    );

    $this->artisan(command: 'languages:generate')
        ->expectsOutput(output: 'Language files created!')
        ->assertExitCode(exitCode: $this->commandSuccess);

    expect(value: file_exists(filename: $jsonPath))
        ->toBeTrue();

    $content = json_decode(
        json       : File::get(path: $jsonPath),
        associative: true,
        depth      : 5100,
        flags      : JSON_THROW_ON_ERROR
    );

    expect(value: file_exists(filename: $jsonPath))
        ->toBeTrue()
        ->and(value: $content)
        ->toHaveKey(key: 'auth.login')
        ->and(value: $content)
        ->toHaveKey(key: 'auth.register')
        ->and(value: $content)
        ->toHaveKey(key: 'validation.required');

    File::deleteDirectory(directory: storage_path(path: 'testing'));
    File::delete(paths: $jsonPath);
});
