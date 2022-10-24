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

    /**
     * @param  string  $viewName
     * @param  string  $pixel
     * @return string
     *
     * @throws \Illuminate\View\ViewException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function compile(string $viewName, string $pixel): string
    {
        $this->filesystem->ensureDirectoryExists(resource_path('views/vendor/mailytics/'));

        if (! $this->viewFactory->exists($viewName)) {
            throw new ViewException("The view: $viewName does not exist.");
        }

        $viewPath = view($viewName)->getPath();

        $cache = $this->cacheRepository->get($viewName);

        // Generate new cache
        if (! is_array($cache)) {
            $cache = [
                'file' => $this->generateMailyticsTemplate($viewPath, $pixel),
                'hash' => md5_file($viewPath),
            ];
        }

        $cachedFileName = $cache['file'] ?? $this->generateMailyticsTemplate($viewPath, $pixel);
        $hash = $cache['hash'] ?? md5_file($viewPath);

        $cachedFileExists = $this->filesystem->exists(resource_path("views/vendor/mailwind/generated/$cachedFileName"));

        if ($cachedFileExists === false) {
            $cachedFileName = $this->generateMailyticsTemplate($viewPath, $pixel);
        }

        // Contents of file changed,
        if ($hash !== md5_file($viewPath)) {
            $hash = md5_file($viewPath);
            $cachedFileName = $this->generateMailyticsTemplate($viewPath, $pixel);
        }

        $view = Str::remove('.blade.php', $cachedFileName);

        $this->cacheManager->set($viewName, [
            'file' => $cachedFileName,
            'hash' => $hash,
        ]);

        return "mailytics::generated.$view";
    }

    /**
     * @param  string  $viewPath
     * @param  string  $pixel
     * @return string
     */
    public function generateMailyticsTemplate(string $viewPath, string $pixel): string
    {
        $fileName = Str::random().'.blade.php';
        $cachedFilePath = resource_path("views/vendor/mailytics/generated/$fileName");

        $fileContent = $this->sanitizeURLs(file_get_contents($viewPath), $pixel);
        file_put_contents($cachedFilePath, $fileContent);

        // Inject Component into cached file
        file_put_contents($cachedFilePath, '<x-mailytics::pixel :url="$mailytics_url"/>', FILE_APPEND);

        return $fileName;
    }

    /**
     * @param  string  $fileContent
     * @param  string  $pixel
     * @return array|string|string[]|null
     */
    private function sanitizeURLs(string $fileContent, string $pixel): array|string|null
    {
        // Sanitize anchor tags using double quotes. E.g. <a href="https://example.com">example</a>
        $fileContent = preg_replace_callback('/href="([^"]*)"/', function ($match) use ($pixel) {
            $url = route('mailytics.clicked', [
                'pixel' => $pixel,
            ]);

            return "href=\"$url?redirect_uri=$match[1]\" ";
        }, $fileContent);

        // Sanitize blade url using double quotes. E.g. <x-mail::button :url="https://stackoverflow.com">View</x-mail::button>
        $fileContent = preg_replace_callback('/:url="([^"]*)"/', function ($match) use ($pixel) {
            $url = route('mailytics.clicked', [
                'pixel' => $pixel,
            ]);

            return ":url=\"$url?redirect_uri=$match[1]\"";
        }, $fileContent);

        // Sanitize anchor tags using single quotes. E.g. <a href='https://example.com'>example</a>
        $fileContent = preg_replace_callback('/href=\'([^"]*)\'/', function ($match) use ($pixel) {
            $url = route('mailytics.clicked', [
                'pixel' => $pixel,
            ]);

            return "href='$url?redirect_uri=$match[1]'";
        }, $fileContent);

        // Sanitize blade url using double quotes. E.g. <x-mail::button :url='https://stackoverflow.com'>View</x-mail::button>
        return preg_replace_callback('/:url=\'([^"]*)\'/', function ($match) use ($pixel) {
            $url = route('mailytics.clicked', [
                'pixel' => $pixel,
            ]);

            return ":url='$url?redirect_uri=$match[1]'";
        }, $fileContent);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function generateImagePixelFile(): string
    {
        $this->filesystem->ensureDirectoryExists(storage_path('app/public/mailytics/'));
        $pixel = Str::uuid().'.jpg';
        $created = copy(dirname(__DIR__).'/pixel.png', storage_path("app/public/mailytics/$pixel"));

        if (! $created) {
            throw new \Exception('Failed to create image signature');
        }

        return $pixel;
    }
}
