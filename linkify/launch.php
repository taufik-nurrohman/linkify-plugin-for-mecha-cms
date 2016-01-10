<?php

// Load the configuration data
$linkify_config = File::open(__DIR__ . DS . 'states' . DS . 'config.txt')->unserialize();

// Regular Expression: `https://github.com/jmrware/LinkifyURL`
// Updated by Taufik Nurrohman: `https://github.com/tovic`
// Known bug: Two URL(s) separated by space will not be linkified correctly
function do_linkify($content) {
    global $config;
    $s = '((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)';
    return preg_replace(
        array(
            '/(\()' . $s . '(\))|(\[)' . $s . '(\])|(\{)' . $s . '(\})|(<|&(?:lt|\#60|\#x3c);)' . $s . '(>|&(?:gt|\#62|\#x3e);)|((?: ^|[^=\s\'"\]])\s*[\'"]?|[^=\s]\s+)(\b(?:ht|f)tps?:\/\/[a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+(?:(?!&(?:gt|\#0*62|\#x0*3e);|&(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]);[.!&\',:?;]?(?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$))&[a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]*)*[a-z0-9\-_~$()*+=\/#[\]@%])/im',
            '#<a class="auto-link" href="(https?\:\/\/)(?!' . preg_quote($config->host, '/') . ')#'
        ),
        array(
            '$1$4$7$10$13<a class="auto-link" href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12',
            '<a class="auto-link" rel="nofollow" href="$1' // Add `rel="nofollow"` attribute to external link(s)
        ),
    $content);
}

Filter::add($linkify_config['scopes'], 'do_linkify');