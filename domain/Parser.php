<?php

namespace andytruong\dict\domain;

use Symfony\Component\DomCrawler\Crawler;

class Parser
{
    public static function fixRules(array $rules)
    {
        $return = [];

        $fix = function ($str, $leafValue = null) use (&$fix) {
            $array = [];
            $copy = &$array;

            $str = str_replace('~', ' ', $str);
            $chunks = explode(' -> ', $str);
            foreach ($chunks as $i => $chunk) {
                if ($i + 1 < count($chunks)) {
                    $copy[$chunk] = '';
                    $copy = &$copy[$chunk];
                }
                else {
                    if (null !== $leafValue) {
                        $copy[$chunk] = $leafValue;
                    }
                    else {
                        $copy = trim($chunk);
                    }
                }
            }

            return $array;
        };

        foreach ($rules as $key => $rule) {
            $rule = is_array($rule) ? static::fixRules($rule) : $rule;

            if (is_numeric($key)) {
                if (!is_string($rule)) {
                    $return[$key] = $rule;
                    continue;
                }

                $return = array_merge_recursive($return, $fix($rule));
            }
            elseif (is_string($key)) {
                $return = array_merge_recursive($return, $fix($key, $rule));
            }
        }

        return $return;
    }

    public function parse(Crawler $node, $rules, array &$return = [])
    {
        if (is_string($rules)) {
            return (':attr' === $rules)
                ? $node->attr($rules)
                : $node->filter($rules)->count() ? $node->filter($rules)->text() : null;
        }
        elseif (is_callable($rules)) {
            return $rules($node);
        }

        foreach ($rules as $key => &$rule) {
            switch ($key) {
                case 0 === strpos($key, '@'):
                    $this->property($node, $key, $rule, $return);
                    break;

                case ':first':
                    return $this->parse($node->first(), $rule, $return);

                case ':next-sibling':
                    return $this->parse($node->siblings()->first(), $rule, $return);

                case ':each':
                    return $this->each($node, $rule);

                case':attr':
                    return $node->attr($rule);

                default:
                    return $this->parse($node->filter($key), $rule, $return);
            }
        }

        return $return;
    }

    private function each(Crawler $node, $rule)
    {
        $elements = [];

        // '@idioms' => [':each' => '#relatedentries > dl > dd > ul > li > a > .arl8']
        if (is_string($rule)) {
            $node->filter($rule)->each(function (Crawler $node) use ($elements) {
                $value = $node->text();
                if (null !== $value) {
                    $elements[] = $value;
                }
            });
        }
        // ':each' => ['.x-gs' => [':each' => '.rx-g .x']]]
        else {
            foreach ($rule as $filter => $pattern) {
                $node->filter($filter)->each(function (Crawler $node, $i) use (&$elements, $pattern) {
                    $elements[$i] = [];
                    $this->parse($node, $pattern, $elements[$i]);
                });
            }
        }

        return $elements;
    }

    private function property(Crawler $node, $key, $rule, array &$return)
    {
        $property = substr($key, 1);

        if ($property) {
            $value = $this->parse($node, $rule);
        }
        else {
            $value = $this->parse($node, $rule, $return);
        }

        if (null !== $value) {
            if (empty($property)) {
                $return = $value;
            }
            else {
                $return[$property] = $value;
            }
        }

        return $return;
    }
}
