<?php

error_reporting(E_USER_WARNING);

/**
 * 'wrap' function that takes two arguments, $string and $length.
 * The function should return the string, but with new lines ("\n") added to ensure that no line is longer than $length
 * characters. Always wrap at word boundaries if possible, only break a word if it is longer than $length characters.
 * When breaking at word boundaries, replace all the whitespace between the two words with a single newline character.
 * Any unbroken whitespace should be left unchanged.
 *
 * @param string $string
 * @param int $length
 * @return string|null
 */
function wrap(string $string, int $length): ?string
{
    if ($length < 1) {
        trigger_error('Can\'t force cut when width is zero', E_USER_WARNING);

        return null;
    }

    // We use hard-coded newline feed "\n", to avoid put "\n" in lots of places, we here assign it to a variable
    $newLine = "\n";

    $lineLength = 0;

    return preg_replace_callback('/(\s*)(\S*)/', function ($matches) use ($length, $newLine, &$lineLength) {
        // Usually there should be one whitespace, but it could be possibly any number start from 0.
        // When it is 0; it is an newline already in the given string.
        $spacesLength = mb_strlen($matches[1]);
        $wordLength = mb_strlen($matches[2]);

        if ($lineLength + $spacesLength + $wordLength <= $length) {
            $lineLength += $spacesLength + $wordLength;

            return $matches[0];
        }

        // Case: $lineLength + $spacesLength + $wordLength > $length, break with different cases of how long
        // the word is. This also need to handle multiple adjacent spaces between the words, we handle it first
        $remainingLength = $length - $lineLength;
        $returnPart1 = '';

        if ($spacesLength) {
            $index = 0;
            $returnPart1 = preg_replace_callback(
                '/\s/',
                function ($spaceMatches) use ($newLine, $length, $remainingLength, $spacesLength, &$index) {
                    if ($index % ($length + 1) == $remainingLength || $index == $spacesLength - 1) {
                        $index++;

                        return $newLine;
                    }
                    $index++;

                    return '';
                },
                $matches[1]
            );
        }

        if ($wordLength <= $length) {
            $returnPart2 = $matches[2];
            $lineLength = $wordLength;
        } else { // Case: wordLength > $length
            $partsSplit = str_split($matches[2], $length);
            $returnPart2 = implode($newLine, $partsSplit);
            $lineLength = mb_strlen($partsSplit[count($partsSplit) - 1]);
        }

        return $returnPart1 . $returnPart2;
    }, $string);
}
