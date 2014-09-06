<?php

// Load the configuration file
$linkify_config = File::open(PLUGIN . DS . 'linkify' . DS . 'states' . DS . 'config.txt')->unserialize();

// Regular Expression credit to `https://github.com/jmrware/LinkifyURL`
// Known bug: Two URL(s) separated by spaces will not be linkified correctly
$linkify_url_pattern = '/# Rev:20100913_0900 github.com\/jmrware\/LinkifyURL
# Match http & ftp URL that is not already linkified.
  # Alternative 1: URL delimited by (parentheses).
  (\()                     # $1  "(" start delimiter.
  ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $2: URL.
  (\))                     # $3: ")" end delimiter.
| # Alternative 2: URL delimited by [square brackets].
  (\[)                     # $4: "[" start delimiter.
  ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $5: URL.
  (\])                     # $6: "]" end delimiter.
| # Alternative 3: URL delimited by {curly braces}.
  (\{)                     # $7: "{" start delimiter.
  ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $8: URL.
  (\})                     # $9: "}" end delimiter.
| # Alternative 4: URL delimited by <angle brackets>.
  (<|&(?:lt|\#60|\#x3c);)  # $10: "<" start delimiter (or HTML entity).
  ((?:ht|f)tps?:\/\/[a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]+)  # $11: URL.
  (>|&(?:gt|\#62|\#x3e);)  # $12: ">" end delimiter (or HTML entity).
| # Alternative 5: URL not delimited by (), [], {} or <>.
  (                        # $13: Prefix proving URL not already linked.
    (?: ^                  # Can be a beginning of line or string, or
    | [^=\s\'"\]]          # a non-"=", non-quote, non-"]", followed by
    ) \s*[\'"]?            # optional whitespace and optional quote;
  | [^=\s]\s+              # or... a non-equals sign followed by whitespace.
  )                        # End $13. Non-prelinkified-proof prefix.
  ( \b                     # $14: Other non-delimited URL.
    (?:ht|f)tps?:\/\/      # Required literal http, https, ftp or ftps prefix.
    [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]+ # All URI chars except "&" (normal*).
    (?:                    # Either on a "&" or at the end of URI.
      (?!                  # Allow a "&" char only if not start of an...
        &(?:gt|\#0*62|\#x0*3e);                  # HTML ">" entity, or
      | &(?:amp|apos|quot|\#0*3[49]|\#x0*2[27]); # a [&\'"] entity if
        [.!&\',:?;]?        # followed by optional punctuation then
        (?:[^a-z0-9\-._~!$&\'()*+,;=:\/?#[\]@%]|$)  # a non-URI char or EOS.
      ) &                  # If neg-assertion true, match "&" (special).
      [a-z0-9\-._~!$\'()*+,;=:\/?#[\]@%]* # More non-& URI chars (normal*).
    )*                     # Unroll-the-loop (special normal*)*.
    [a-z0-9\-_~$()*+=\/#[\]@%]  # Last char can\'t be [.!&\',;:?]
  )                        # End $14. Other non-delimited URL.
/imx';
$linkify_url_replace = '$1$4$7$10$13<a class="auto-link" href="$2$5$8$11$14">$2$5$8$11$14</a>$3$6$9$12';

// Do auto linking
foreach($linkify_config['scopes'] as $scope) {
    Filter::add($scope, function($content) use($config, $linkify_url_pattern, $linkify_url_replace) {
        return preg_replace(
            array(
                $linkify_url_pattern,
                '#<a class="auto-link" href="(https?\:\/\/)(?!' . preg_quote($config->host, '/') . ')#'),
            array(
                $linkify_url_replace,
                '<a class="auto-link" rel="nofollow" href="$1'), // Add `rel="nofollow"` attribute in external links
        $content);
    });
}


/**
 * Plugin Updater
 * --------------
 */

Route::accept($config->manager->slug . '/plugin/linkify/update', function() use($config, $speak) {
    if( ! Guardian::happy()) {
        Shield::abort();
    }
    if($request = Request::post()) {
        Guardian::checkToken($request['token']);
        unset($request['token']); // Remove token from request array
        if( ! isset($request['scopes'])) {
            $request['scopes'] = array();
        }
        File::serialize($request)->saveTo(PLUGIN . DS . 'linkify' . DS . 'states' . DS . 'config.txt', 0600);
        Notify::success(Config::speak('notify_success_updated', array($speak->plugin)));
        Guardian::kick(dirname($config->url_current));
    }
});