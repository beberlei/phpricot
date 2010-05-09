# PHPricot - A forgiving HTML Parsing library

There are plenty of scenarios when you get passed ugly HTML and need
to parse it, modify it and re-write it to how it looked before. Take
for example HTML E-Mails - a magic box of strange behaviours across
all the important clients like Outlook 2007+ and Webmailers.

PHPricot to the rescue, using the pecl/html_parse extension based on the EKHtml Parser to build
an Abstract Syntax Tree of your HTML, no matter how ugly it is, and
re-writes this AST into your ugly HTML with only minor changes:

* All attributes will be enclosed by ""
* Tags get lower-cased
* Self-Closing Tags (currently) always get /&gt; endings
* Multi-line tags are wrapped into a single line
* Really broken tags like &lt;a href="foo&lt;a href="bar.html"&gt; are separated
  into &lt;a href="foo"&gt;&lt;a href="bar.html"&gt;
* Closing tags with no matching opening tag are omitted.
* Comments always get enclosed by &lt;!-- --&gt;

Additionally you can hook into events for each start tag, end tag, comment
and text elements.

PHPricot is motivated by Rubys HPricot but only a distantly related port.

## Installation

This currently a linux only installation guide (please contribute Windows or MacOS if
you find the time).

1. Install [EKHtml](http://ekhtml.sourceforge.net/)

2. Install [pecl/html_parse](http://pecl.php.net/package/html_parse)

    pecl install html_parse

## Usage

Usage is simple:

    $p = new PHPricot_Parser();
    $doc = $p->parse($html);
    echo $doc->toHtml(); // render the AST to html
    echo $doc->toText(); // echo a text representation of your html

This however does not modify your HTML at all (other than the changes described above).
You can register events by calling the parser:

    $p = new PHPRicot_Parser();
    $p->addListener(new PHPricot_Listeners_DebugNodes());
    $doc = $p->parse($html);

There are four interfaces that act as marker for the four different events occouring:

* PHPricot_Listeners_StartTagListener for "startTag" tokens
* PHPricot_Listeners_EndTagListener for "endTag" tokens
* PHPricot_Listeners_TextListener for "text" tokens
* PHPricot_Listeners_CommentListener for "comment" tokens

You can for example use the SearchTags listener to get find all links in a document:

    $search = new PHPricot_Listeners_SearchTags(array('a'));
    $p = new PHPRicot_Parser();
    $p->addListener($search);
    $doc = $p->parse($html);

    $urls = array();
    foreach ($search->getTags('a') AS $a) {
        $urls[] = $a->attributes['href'];
    }

## TODOS

* Integrate a CSS Selector Parser to find `Element` Tag nodes
* Add Traversing API
* Add HTML context details (what tags are are really HTML tags)
* Attempt to fix some broken HTML if possible