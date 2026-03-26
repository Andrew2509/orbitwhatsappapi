<?php

namespace App\Services;

use App\Models\BlacklistedWord;
use Illuminate\Support\Str;

/**
 * Service for filtering message content against blacklisted words.
 * 
 * This helps prevent users from sending spam, scam, or inappropriate content
 * through the WhatsApp API service.
 */
class MessageContentFilter
{
    /**
     * Result constants
     */
    public const RESULT_CLEAN = 'clean';
    public const RESULT_WARNING = 'warning';
    public const RESULT_BLOCKED = 'blocked';

    /**
     * Check message content against blacklist.
     *
     * @param string $content Message content to check
     * @return array{result: string, matched_words: array, categories: array}
     */
    public function check(string $content): array
    {
        if (!config('whatsapp.content_filter_enabled', true)) {
            return [
                'result' => self::RESULT_CLEAN,
                'matched_words' => [],
                'categories' => [],
            ];
        }

        $blacklistedWords = BlacklistedWord::getAllActive();
        $normalizedContent = $this->normalizeText($content);
        
        $matchedWords = [];
        $categories = [];
        $highestSeverity = null;

        foreach ($blacklistedWords as $wordData) {
            $normalizedWord = $this->normalizeText($wordData['word']);
            
            // Check if word exists in content
            if ($this->containsWord($normalizedContent, $normalizedWord)) {
                $matchedWords[] = $wordData['word'];
                $categories[] = $wordData['category'];
                
                // Track highest severity
                if ($wordData['severity'] === 'block') {
                    $highestSeverity = 'block';
                } elseif ($highestSeverity !== 'block') {
                    $highestSeverity = 'warning';
                }
            }
        }

        // Determine result
        $result = self::RESULT_CLEAN;
        if (!empty($matchedWords)) {
            $result = $highestSeverity === 'block' ? self::RESULT_BLOCKED : self::RESULT_WARNING;
        }

        return [
            'result' => $result,
            'matched_words' => array_unique($matchedWords),
            'categories' => array_unique($categories),
        ];
    }

    /**
     * Quick check if content is safe to send.
     */
    public function isSafe(string $content): bool
    {
        $check = $this->check($content);
        
        // Check config for action type
        $action = config('whatsapp.content_filter_action', 'block');
        
        if ($action === 'block') {
            return $check['result'] !== self::RESULT_BLOCKED;
        }
        
        // If action is 'warn', always allow (just log warning)
        return true;
    }

    /**
     * Check content and throw exception if blocked.
     *
     * @throws \App\Exceptions\ContentBlockedException
     */
    public function checkOrFail(string $content): array
    {
        $check = $this->check($content);
        
        if ($check['result'] === self::RESULT_BLOCKED) {
            $categories = implode(', ', $check['categories']);
            throw new \Exception(
                "Pesan diblokir karena mengandung konten terlarang (kategori: {$categories})"
            );
        }
        
        return $check;
    }

    /**
     * Normalize text for comparison.
     * Removes special characters, converts to lowercase, etc.
     */
    protected function normalizeText(string $text): string
    {
        // Convert to lowercase
        $text = Str::lower($text);
        
        // Replace common character substitutions used to bypass filters
        $substitutions = [
            '0' => 'o',
            '1' => 'i',
            '3' => 'e',
            '4' => 'a',
            '5' => 's',
            '7' => 't',
            '@' => 'a',
            '$' => 's',
        ];
        $text = str_replace(array_keys($substitutions), array_values($substitutions), $text);
        
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        return trim($text);
    }

    /**
     * Check if content contains a specific word.
     * Uses word boundary matching to avoid false positives.
     */
    protected function containsWord(string $content, string $word): bool
    {
        // Use word boundary for better matching
        $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
        return preg_match($pattern, $content) === 1;
    }

    /**
     * Get all categories with their labels.
     */
    public static function getCategories(): array
    {
        return BlacklistedWord::CATEGORIES;
    }
}
