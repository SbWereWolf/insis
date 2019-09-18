<?php

/* Можно было логику в пару классов завернуть, но что то лень */
const SECOND = 'second';
const S = 's';
const FIRST = 'first';
const F = 'f';
const HELP = 'help';
const H = 'h';
const REQUIRED = ':';

const RADIX = 10;
const LONG = " --";
const SHORT = "-";
$params = array(
    F . REQUIRED => FIRST . REQUIRED,
    S . REQUIRED => SECOND . REQUIRED,
    H => HELP
);

$options = getopt(implode('', array_keys($params)), $params);
$letHelp = empty($options)
    || array_key_exists(HELP, $options)
    || array_key_exists(H, $options);
if ($letHelp) {
    echo PHP_EOL . help();
}

$hasFirst = array_key_exists(F, $options)
    || array_key_exists(FIRST, $options);
$hasSecond =
    array_key_exists(S, $options)
    || array_key_exists(SECOND, $options);

$isValid = false;
$mayRun = $hasFirst && $hasSecond;

if ($mayRun) {
    $first = array_key_exists(F, $options)
        ? $options[F]
        : $options[FIRST];
    $isValid = validate($first);
}
if ($mayRun && !$isValid) {
    echo PHP_EOL . 'First argument is invalid';
}
if ($isValid) {
    $second = array_key_exists(S, $options)
        ? $options[S]
        : $options[SECOND];
    $isValid = validate($second);
}
if ($mayRun && !$isValid) {
    echo PHP_EOL . 'Second argument is invalid';
}

if (!$mayRun && !$letHelp) {

    echo PHP_EOL . 'Not enough arguments';
    echo PHP_EOL . help();
}

if ($isValid) {
    $summ = bigNumberSummator($first, $second);
    echo PHP_EOL . "$first + $second = $summ";
}

function help(): string
{
    return "
    example of using :
    " . __FILE__ . " -f 9999999999999999999999999999999999999999 -s 1
    
    " . SHORT . H . ' ' . LONG . HELP . "       this script using description
    " . SHORT . F . ' ' . LONG . FIRST . "      the first argument to summ
    " . SHORT . S . ' ' . LONG . SECOND . "     the second argument to summ
";
}

function validate(string $subject): bool
{
    $matches = preg_match('/\D/', $subject);
    $isValid = $matches === 0;

    return $isValid;
}

function bigNumberSummator(string $argumentA, string $argumentB): string
{
    $symbolsOfA = reverseSymbols($argumentA);
    $symbolsOfB = reverseSymbols($argumentB);

    $limit = count($symbolsOfA) > count($symbolsOfB)
        ? count($symbolsOfA)
        : count($symbolsOfB);

    $acc = 0;
    $summ = '';
    for ($index = 0; $index < $limit; $index++) {
        $a = getInt($symbolsOfA, $index);
        $b = getInt($symbolsOfB, $index);

        $next = ($a + $b + $acc) % RADIX;
        $acc = (int)(floor(($a + $b + $acc) / RADIX));

        $summ = ((string)$next) . $summ;

        echo PHP_EOL . "$a + $b = $next , acc=$acc";
    }
    if ($acc !== 0) {
        $summ = ((string)$acc) . $summ;
    }

    return $summ;
}

function getInt(array $symbols, int $index): int
{
    return array_key_exists($index, $symbols)
        ? (int)$symbols[$index]
        : 0;
}

function reverseSymbols(string $symbols): array
{
    return array_reverse(str_split($symbols));
}
