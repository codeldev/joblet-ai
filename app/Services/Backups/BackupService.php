<?php

declare(strict_types=1);

namespace App\Services\Backups;

use App\Contracts\Services\Backups\BackupServiceInterface;
use App\Exceptions\Backups\MissingBackupConfigurationException;
use App\Facades\Gdrive;
use App\Facades\MySqlDumper;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use ZanySoft\Zip\Facades\Zip;

final class BackupService implements BackupServiceInterface
{
    public ?string $error = null;

    private ?string $filePath = null;

    private ?string $fileName = null;

    private ?string $sqlName = null;

    private ?string $sqlPath = null;

    private ?string $envPath = null;

    private Filesystem $disk;

    /** @var array<string, string|int> */
    private array $dbConfig;

    public function __construct()
    {
        $this->setup();
    }

    public function __invoke(): bool
    {
        if (notEmpty(value: $this->error))
        {
            return false;
        }

        if (! $this->createDatabaseDump())
        {
            return false;
        }

        if (! $this->createBackupFile())
        {
            return false;
        }

        return $this->storeBackup();
    }

    private function setup(): void
    {
        $this
            ->setDisk()
            ->setZipPath()
            ->setEnvPath()
            ->setSqlPath()
            ->setDbConfig();
    }

    public function setFileName(string $fileName): self
    {
        if (notEmpty(value: $fileName))
        {
            $this->fileName = $fileName;
            $this->filePath = $this->disk->path(path: $this->fileName);
        }

        return $this;
    }

    public function setSqlName(string $sqlName): self
    {
        if (notEmpty(value: $sqlName))
        {
            $this->sqlName = $sqlName;
            $this->sqlPath = $this->disk->path(path: $this->sqlName);
        }

        return $this;
    }

    private function createBackupFile(): bool
    {
        try
        {
            $zip = Zip::create(zip_file: $this->filePath);
            $zip->add(file_path: $this->envPath, flatroot: true);
            $zip->add(file_path: $this->sqlPath, flatroot: true);
            $zip->add(file_path: storage_path(path: 'app/private'));
            $zip->add(file_path: storage_path(path: 'app/public'));
            $zip->close();

            return Zip::check(zip_file: $this->filePath);
        }
        catch (Exception $e)
        {
            return $this->setErrorResponse(e: $e);
        }
    }

    private function createDatabaseDump(): bool
    {
        try
        {
            MySqlDumper::create()
                ->setHost(host: (string) $this->dbConfig['host'])
                ->setPort(port: (int) $this->dbConfig['port'])
                ->setDbName(dbName: (string) $this->dbConfig['database'])
                ->setUserName(userName: (string) $this->dbConfig['username'])
                ->setPassword(password: (string) $this->dbConfig['password'])
                ->dumpToFile(dumpFile: (string) $this->sqlPath);

            return true;
        }
        catch (Exception $e)
        {
            return $this->setErrorResponse(e: $e);
        }
    }

    private function storeBackup(): bool
    {
        try
        {
            $uploadToConfig = config(key: 'backups.upload_to');

            if (! is_string(value: $uploadToConfig))
            {
                $uploadToConfig = 'backups/:file';
            }

            $storeAs = trans(
                key     : $uploadToConfig,
                replace : ['file' => (string) $this->fileName]
            );

            Gdrive::put(
                path: $storeAs,
                file: $this->filePath
            );

            $this->cleanup();

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

        $this->cleanup();

        return false;
    }

    private function setDisk(): self
    {
        $this->disk = Storage::disk(name: 'local');

        return $this;
    }

    private function setZipPath(): self
    {
        try
        {
            /** @var array<string, mixed>|null $config */
            $config = config(key: 'backups');

            if (! is_array(value: $config))
            {
                throw new MissingBackupConfigurationException;
            }

            $dateFormat = (isset($config['date_format']) && is_string(value: $config['date_format']))
                ? $config['date_format']
                : 'Y-m-d-H-i-s';

            $zipFileKey = (isset($config['zip_file']) && is_string(value: $config['zip_file']))
                ? $config['zip_file']
                : 'backup-:date.zip';

            $this->fileName = trans(key: $zipFileKey, replace: [
                'date' => now()->format(format: $dateFormat),
            ]);

            $this->filePath = $this->disk->path(path: $this->fileName);
        }
        catch (Exception $e)
        {
            $this->setErrorResponse(e: $e);
        }

        return $this;
    }

    private function setEnvPath(): self
    {
        $this->envPath = base_path(path: '.env');

        return $this;
    }

    private function setSqlPath(): self
    {
        $this->sqlName = 'database.sql';
        $this->sqlPath = $this->disk->path(path: $this->sqlName);

        return $this;
    }

    private function setDbConfig(): void
    {
        /** @var array<string, string|int> $config */
        $config = config(key: 'database.connections.mysql') ?? [];

        $this->dbConfig = $config;
    }

    private function cleanup(): void
    {
        if ($this->fileName !== null)
        {
            $this->disk->delete(paths: $this->fileName);
        }

        if ($this->sqlName !== null)
        {
            $this->disk->delete(paths: $this->sqlName);
        }
    }
}
