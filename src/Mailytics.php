<?php

namespace Icodestuff\Mailytics;

use Illuminate\Cache\CacheManager;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\ViewException;

class Mailytics
{
    public function __construct(
        protected Filesystem $filesystem,
        protected ViewFactory $viewFactory,
        protected CacheManager $cacheManager,
        protected CacheRepository $cacheRepository,
        protected Kernel $kernel
    ) {
    }
    // it should compile email with a 1x1 pixel attached
    // it should grab all urls and sanitize with custom mailytics url
    // add mailable_class to migrations
    // add stats cards for emails
    // add paginated table for sent emails

    /**
     * @param  string  $viewName
     * @return string
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function compile(string $viewName, bool $regenerate = false): string
    {
        $this->filesystem->ensureDirectoryExists(resource_path('views/vendor/mailytics/'));

        if (! $this->viewFactory->exists($viewName)) {
            throw new ViewException("The view: $viewName does not exist.");
        }

        $viewPath = view($viewName)->getPath();

        $cache = $regenerate ? null : $this->cacheRepository->get($viewName);

        // Generate new cache
        if (! is_array($cache)) {
            $cache = [
                'file' => $this->generateMailyticsTemplate($viewPath),
                'hash' => md5_file($viewPath),
            ];
        }

        $cachedFileName = $cache['file'] ?? $this->generateMailyticsTemplate($viewPath);
        $hash = $cache['hash'] ?? md5_file($viewPath);

        $cachedFileExists = $this->filesystem->exists(resource_path("views/vendor/mailwind/generated/$cachedFileName"));

        if ($cachedFileExists === false) {
            $cachedFileName = $this->generateMailyticsTemplate($viewPath);
        }

        // Contents of file changed,
        if ($hash !== md5_file($viewPath)) {
            $hash = md5_file($viewPath);
            $cachedFileName = $this->generateMailyticsTemplate($viewPath);
        }

        $view = Str::remove('.blade.php', $cachedFileName);

        $this->cacheManager->set($viewName, [
            'file' => $cachedFileName,
            'hash' => $hash,
        ]);

        return "mailytics::generated.$view";
    }

    public function generateMailyticsTemplate(string $viewPath)
    {
        $fileName = Str::random().'.blade.php';
        $cachedFilePath = resource_path("views/vendor/mailytics/generated/$fileName");

        // copy view path to new file name
        copy($viewPath, $cachedFilePath);

        // Inject Component into cached file
        file_put_contents($cachedFilePath, '<x-mailytics::image-signature :url="$mailytics_url"/>', FILE_APPEND);

        return $fileName;
    }

    public function generateImageSignatureFile(): string
    {
        $this->filesystem->ensureDirectoryExists(storage_path('app/public/mailytics/'));
        $imageSignature = Str::uuid().'.jpg';
        $created = copy(dirname(__DIR__) .'/pixel.png', storage_path("app/public/mailytics/$imageSignature"));

        if (! $created) {
            throw new \Exception('Failed to create image signature');
        }

        return $imageSignature;
    }
}
