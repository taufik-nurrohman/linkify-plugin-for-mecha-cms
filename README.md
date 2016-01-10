Linkify Plugin for Mecha CMS
============================

> This plugin will automatically replace your plain link URL into a clickable link.

_Regular Expression_ credit to <https://github.com/jmrware/LinkifyURL>

#### Before

~~~ .html
Lorem ipsum http://example.com dolor http://localhost/foo/bar sit amet.
~~~

#### After

~~~ .html
Lorem ipsum <a class="auto-link" rel="nofollow" href="http://example.com">http://example.com</a> dolor <a class="auto-link" href="http://localhost/foo/bar">http://localhost/foo/bar</a> sit amet.
~~~