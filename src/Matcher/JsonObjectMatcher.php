<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;
use Coduo\PHPMatcher\Parser;

final class JsonObjectMatcher extends Matcher
{
    const JSON_PATTERN = 'json';

    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function match($value, $pattern) : bool
    {
        if (!$this->isJsonPattern($pattern)) {
            return false;
        }

        if (!Json::isValid($value) && !\is_null($value) && !\is_array($value)) {
            $this->error = \sprintf('Invalid given JSON of value. %s', Json::getErrorMessage());
            return false;
        }

        if ($this->isJsonPattern($pattern)) {
            return $this->allExpandersMatch($value, $pattern);
        }

        return false;
    }

    public function canMatch($pattern) : bool
    {
        return \is_string($pattern) && $this->isJsonPattern($pattern);
    }

    private function isJsonPattern($pattern)
    {
        if (!\is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::JSON_PATTERN);
    }

    private function allExpandersMatch($value, $pattern)
    {
        $typePattern = $this->parser->parse($pattern);

        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            return false;
        }

        return true;
    }
}
