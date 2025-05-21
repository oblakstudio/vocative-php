<?php //phpcs:disable WordPress.NamingConventions, Squiz.Commenting, SlevomatCodingStandard.Functions.RequireMultiLineCall.RequiredMultiLineCall, WordPress.WP.AlternativeFunctions, SlevomatCodingStandard.Arrays.AlphabeticallySortedByKeys.IncorrectKeyOrder, WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound

namespace Oblak\Vocative;

use Oblak\Transliterator;
use Oblak\Vocative\Interfaces\Dictionary;

/**
 * Class Vokativ - Creates vocative case forms for names
 *
 * @package Avram\Vokativ
 */
class Vocative
{
    public const SOURCE_ALGORITHM  = 'algorithm';
    public const SOURCE_DICTIONARY = 'dictionary';

    /**
     * Mapping of suffix patterns to their replacements with minimum length requirements
     *
     * @var array<string, array<string, int>>
     */
    private const SUFFIX_RULES = [
        'ICA'  => [ 'ICE' => 4 ],
        'TRA'  => [ 'TRA' => 0 ],
        'TAR'  => [ 'TRE' => 0 ],
        'RA'   => [ 'RA' => 6, 'RO' => 0 ],
        'KA'   => [ 'KA' => 5, 'KO' => 0 ],
        'BA'   => [ 'BO' => 0 ],
        'CA'   => [ 'CO' => 0 ],
        'DA'   => [ 'DA' => 5, 'DO' => 0 ],
        'GA'   => [ 'GO' => 0 ],
        'JA'   => [ 'JO' => 0 ],
        'SA'   => [ 'SO' => 0 ],
        'VA'   => [ 'VA' => 6, 'VO' => 0 ],
        'G'    => [ 'ŽE' => 0 ],
        'K'    => [ 'ČE' => 0 ],
    ];

    /**
     * Suffixes that should remain unchanged
     *
     * @var array<string>
     */
    private const VOWEL_ENDINGS = [ 'A', 'O', 'E', 'I' ];

    /**
     * Special consonant endings that require 'U' suffix
     *
     * @var array<string>
     */
    private const SPECIAL_CONSONANTS = [ 'Ć', 'Đ', 'Č', 'DŽ', 'Ž', 'LJ', 'NJ', 'J' ];

    /**
     * Excluded endings that don't follow normal 'JA' to 'JO' rule
     *
     * @var array<string>
     */
    private const JA_EXCLUSIONS = [ 'EJA', 'IJA', 'DJA', 'NJA' ];

    /**
     * Flag to indicate if input is Cyrillic
     *
     * @var bool|null
     */
    public ?bool $cyrillic = null;

    /**
     * Flag to indicate if processing is done
     *
     * @var bool
     */
    private bool $done = false;

    /**
     * Source of vocative form (algorithm or dictionary)
     *
     * @var 'algorithm'|'dictionary'|null
     */
    private ?string $source = null;

    /**
     * Original nominative form of the name
     *
     * @var string|null
     */
    private ?string $nominative = null;

    /**
     * Computed vocative form of the name
     *
     * @var string|null
     */
    private ?string $vocative = null;

    /**
     * Dictionary of exceptions
     *
     * @var Dictionary
     */
    private Dictionary $dictionary;

    /**
     * Vokativ constructor.
     *
     * @param ?Dictionary $dictionary Dictionary of exceptions
     */
    public function __construct(?Dictionary $dictionary = null)
    {
        $this->dictionary = $dictionary ?? new BaseDictionary();
    }

    /**
     * Set a custom dictionary for name exceptions
     *
     * @param Dictionary $dictionary Custom dictionary implementation
     * @return static Current instance for method chaining
     */
    public function withDictionary(Dictionary $dictionary): static
    {
        $this->dictionary = $dictionary;

        return $this;
    }

    /**
     * Get the current dictionary instance
     *
     * @return Dictionary Current dictionary implementation
     */
    public function getDictionary(): Dictionary
    {
        return $this->dictionary;
    }

    /**
     * Get the source of the last vocative form generated
     *
     * @return string|null Algorithm or dictionary
     */
    public function getSource(): ?string
    {
        return $this->source;
    }

    /**
     * Creates a vocative case form from a name in nominative case
     *
     * @param string $nominativ Name in nominative case
     * @param bool   $ignoreDict Ignore dictionary of exceptions
     * @return string Name in vocative case
     */
    public function make(string $nominativ, bool $ignoreDict = false): string
    {
        return $this
            ->initialize($nominativ)
            ->normalizeInput()
            ->checkException($ignoreDict)
            ->transform()
            ->finalize();
    }

    /**
     * Initialize object state for processing a new name
     *
     * @param string $nominative Name in nominative case
     * @return static Current instance for method chaining
     */
    private function initialize(string $nominative): static
    {
        $this->nominative = $nominative;
        $this->done       = false;
        $this->source     = null;
        $this->vocative   = null;
        $this->cyrillic   = $this->isCyrillic($nominative);

        return $this;
    }

    /**
     * Check if name exists in the dictionary of exceptions
     *
     * @param bool $ignoreDict Whether to ignore the dictionary lookup
     * @return static Current instance for method chaining
     */
    private function checkException(bool $ignoreDict): static
    {
        if (!$ignoreDict && $this->dictionary->hasName($this->nominative)) {
            $this->source = self::SOURCE_DICTIONARY;
            return $this->setVocative($this->dictionary->getName($this->nominative));
        }

        return $this;
    }

    /**
     * Transform nominative to vocative form using appropriate rules
     *
     * @return static Current instance for method chaining
     */
    private function transform(): static
    {
        if ($this->done) {
            return $this;
        }

        $this->source = self::SOURCE_ALGORITHM;

        $rule = $this->getRule();

        return match ($rule) {
            'JA'    => $this->transformJa(),
            'KA'    => $this->transformKa(),
            ''      => $this->transformSpecial(),
            default => $this->transformDefault($rule),
        };
    }

    /**
     * Determine which suffix rule applies to the current name
     *
     * @return string The matching suffix rule or empty string if none matches
     */
    private function getRule(): string
    {
        foreach (\array_keys(self::SUFFIX_RULES) as $suffix) {
            if ($this->matchSuffix($this->nominative, $suffix)) {
                return $suffix;
            }
        }

        return '';
    }

    /**
     * Apply transformation rules for names ending with 'JA'
     * Handles special exclusions that don't follow the standard pattern
     *
     * @return static Current instance for method chaining
     */
    private function transformJa(): static
    {
        foreach (self::JA_EXCLUSIONS as $exclusion) {
            if ($this->matchSuffix($this->nominative, $exclusion)) {
                return $this->setVocative($this->nominative);
            }
        }

        return $this->transformDefault('JA');
    }

    /**
     * Apply transformation rules for names ending with 'KA'
     * Length-dependent rules determine the proper replacement
     *
     * @return static Current instance for method chaining
     */
    private function transformKa(): static
    {
        $nominative = $this->nominative;

        foreach (self::SUFFIX_RULES['KA'] as $replacement => $minLength) {
            if (\mb_strlen($nominative) < $minLength) {
                continue;
            }

            return $this->setVocative(\mb_substr($nominative, 0, -2) . $replacement);
        }
        return $this->setVocative($nominative);
    }

    /**
     * Apply default transformation rules based on the identified suffix
     *
     * @param string $suffix The suffix rule to apply
     * @return static Current instance for method chaining
     */
    private function transformDefault(string $suffix): static
    {
        $nominative = $this->nominative;

        foreach (self::SUFFIX_RULES[ $suffix ] as $replacement => $minLength) {
            if (\mb_strlen($nominative) < $minLength) {
                continue;
            }

            // Get the part of the name without the suffix
            $base = \mb_substr($nominative, 0, \mb_strlen($nominative) - \mb_strlen($suffix));

            // Apply replacement by adding to base
            return $this->setVocative($base . $replacement);
        }

        return $this->transformSpecial();
    }

    /**
     * Apply special transformation rules for cases not handled by suffix rules
     * Handles special consonant endings, vowel endings, and default case
     *
     * @return static Current instance for method chaining
     */
    private function transformSpecial(): static
    {
        $nominative = $this->nominative;
        // Handle special consonant endings that require 'U' suffix
        foreach (self::SPECIAL_CONSONANTS as $consonant) {
            if ($this->matchSuffix($nominative, $consonant)) {
                return $this->setVocative($nominative . 'U');
            }
        }

        // Handle vowel endings (no change)
        $lastChar = \mb_substr($nominative, -1);
        if (\in_array($lastChar, self::VOWEL_ENDINGS, true)) {
            return $this->setVocative($nominative);
        }

        // Default: add 'E' suffix
        return $this->setVocative($nominative . 'E');
    }

    /**
     * Set the vocative form and mark processing as complete
     *
     * @param string $vocative The final vocative form
     * @return static Current instance for method chaining
     */
    private function setVocative(string $vocative): static
    {
        $this->vocative = $vocative;
        $this->done     = true;

        return $this;
    }

    /**
     * Check if a text ends with a specific suffix
     *
     * @param string $text The text to check
     * @param string $suffix The suffix to match against
     * @return bool True if the text ends with the suffix
     */
    private function matchSuffix(string $text, string $suffix): bool
    {
        return \mb_substr($text, -\mb_strlen($suffix)) === $suffix;
    }

    /**
     * Finalize the output by adjusting case and script
     *
     * @return string
     */
    private function finalize(): string
    {
        $vocative = $this->cyrillic
            ? Transliterator::latToCir($this->vocative)
            : $this->vocative;

        return \mb_ucfirst(\mb_strtolower($vocative));
    }

    /**
     * Normalize and clean input text
     *
     * @param string|null $input Input text (uses nominative if null)
     * @return static Current instance for method chaining
     */
    private function normalizeInput(?string $input = null): static
    {
        $input ??= $this->nominative;
        $input   = \trim($input);
        $input   = \stripslashes($input);
        $input   = \strip_tags($input);

        // Get first word if multiple words
        if (false !== \mb_strpos($input, ' ')) {
            $tmp   = \explode(' ', $input);
            $input = $tmp[0];
        }

        // Transliterate to Latin
        $input = $this->cyrillic ? Transliterator::cirToLat($input) : $input;

        // Remove non-letter characters
        $input = \preg_replace('/[^a-zA-ZŠĐČĆŽšđčćž]/', '', $input);

        // Convert to uppercase for consistent processing
        $this->nominative = \mb_strtoupper($input, 'utf-8');

        return $this;
    }

    /**
     * Check if text is in Cyrillic script
     *
     * @param string $text Input text
     * @return bool True if Cyrillic
     */
    private function isCyrillic(string $text): bool
    {
        return Transliterator::cirToLat($text) !== $text;
    }
}
