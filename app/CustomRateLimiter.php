<?php
namespace App;

class CustomRateLimiter
{
    private $storageDir;
    private $prefix = 'rate_limit_';

    public function __construct(string $storageDir = null)
    {
        // Use system temp directory if none provided
        $this->storageDir = $storageDir ?? sys_get_temp_dir();
        
        // Ensure storage directory exists and is writable
        if (!is_dir($this->storageDir) || !is_writable($this->storageDir)) {
            throw new \RuntimeException("Storage directory is not writable: {$this->storageDir}");
        }
    }

    /**
     * Check if the rate limit is exceeded for a given key
     *
     * @param string $key Unique identifier for the rate limit (e.g., user ID, IP address)
     * @param int $allowedJobs Maximum number of allowed attempts
     * @param int $windowInSeconds Time window in seconds
     * @return bool True if rate limit exceeded, False otherwise
     */
    public function isLimitExceeded(
        string $key,
        int $allowedJobs = 1,
        int $windowInSeconds = 2
    ): bool {
        $filename = $this->getFilename($key);
        $now = time();
        $attempts = [];

        // Load existing attempts
        if (file_exists($filename)) {
            $content = file_get_contents($filename);
            if ($content !== false) {
                $attempts = json_decode($content, true) ?? [];
            }
        }

        // Remove old attempts
        $attempts = array_filter($attempts, function($timestamp) use ($now, $windowInSeconds) {
            return ($now - $timestamp) < $windowInSeconds;
        });

        // Add new attempt
        $attempts[] = $now;

        // Save attempts back to file
        file_put_contents($filename, json_encode($attempts), LOCK_EX);

        // Check if limit exceeded
        return count($attempts) > $allowedJobs;
    }

    /**
     * Generate storage filename for a key
     *
     * @param string $key Rate limit key
     * @return string Full path to storage file
     */
    private function getFilename(string $key): string
    {
        return $this->storageDir . DIRECTORY_SEPARATOR . $this->prefix . md5($key) . '.json';
    }

    /**
     * Clean up old rate limit files
     */
    public function cleanup(): void
    {
        $files = glob($this->storageDir . DIRECTORY_SEPARATOR . $this->prefix . '*.json');
        $now = time();

        foreach ($files as $file) {
            if (filemtime($file) < ($now - 3600)) { // Remove files older than 1 hour
                unlink($file);
            }
        }
    }
}