<?php

declare(strict_types=1);

namespace App\Services\Backups;

use App\Contracts\Services\Backups\CleanupServiceInterface;
use App\Exceptions\Backups\InvalidBackupConfigurationException;
use App\Exceptions\Backups\MissingBackupConfigurationException;
use App\Exceptions\Backups\MissingGoogleDriveIdException;
use App\Exceptions\Backups\MissingGoogleDriveRefreshTokenException;
use App\Exceptions\Backups\MissingGoogleDriveSecretException;
use App\Facades\Gdrive;
use Exception;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Collection;
use Masbug\Flysystem\GoogleDriveAdapter;

final class CleanupService implements CleanupServiceInterface
{
    public ?string $error = null;

    public function __invoke(): bool
    {
        if (! $this->attemptCleanup())
        {
            return false;
        }

        return $this->clearTrashed();
    }

    private function attemptCleanup(): bool
    {
        try
        {
            $backupsConfig = config(key: 'backups');

            if (! is_array(value: $backupsConfig))
            {
                throw new InvalidBackupConfigurationException;
            }

            $listFromPath = isset($backupsConfig['list_from']) && is_string(value: $backupsConfig['list_from'])
                ? $backupsConfig['list_from']
                : '/';

            /** @var Collection<int,object> $files */
            $files = Gdrive::all(path: $listFromPath);

            if ($files->count() > 1)
            {
                $this->cleanFiles(files: $files);
            }

            return true;
        }
        catch (Exception $e)
        {
            return $this->setErrorResponse(e: $e);
        }
    }

    /** @param Collection<int, object> $files */
    private function cleanFiles(Collection $files): void
    {
        $files->sortByDesc(callback: 'lastModified')
            ->slice(offset: 1)
            ->each(callback: fn (object $file) => $this->deleteFile(file: $file));
    }

    private function deleteFile(object $file): void
    {
        if (method_exists(object_or_class: $file, method: 'path'))
        {
            Gdrive::delete(path: $file->path());
        }
    }

    private function clearTrashed(): bool
    {
        try
        {
            /** @var array<string, mixed>|null $config */
            $config = config(key: 'filesystems.disks.google');

            if (! is_array(value: $config))
            {
                throw new MissingBackupConfigurationException;
            }

            if (! isset($config['clientId']) || ! is_string($config['clientId']))
            {
                throw new MissingGoogleDriveIdException;
            }

            if (! isset($config['clientSecret']) || ! is_string($config['clientSecret']))
            {
                throw new MissingGoogleDriveSecretException;
            }

            if (! isset($config['refreshToken']) || ! is_string($config['refreshToken']))
            {
                throw new MissingGoogleDriveRefreshTokenException;
            }

            $client = new Client;

            $client->setClientId(
                clientId: $config['clientId']
            );

            $client->setClientSecret(
                clientSecret: $config['clientSecret']
            );

            $client->refreshToken(
                refreshToken: $config['refreshToken']
            );

            $service = new Drive(
                clientOrConfig: $client
            );

            $adapter = new GoogleDriveAdapter(
                $service,
                '/'
            );

            $adapter->emptyTrash();

            return true;
        }
        catch (Exception $e)
        {
            return $this->setErrorResponse(e: $e);
        }
    }

    private function setErrorResponse(Exception $e): false
    {
        $this->error = $e->getMessage();

        report(exception: $e);

        return false;
    }
}
