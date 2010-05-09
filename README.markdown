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

### Parse and Render HTML

    $query = new PHPricot_Query($html);
    echo $query->toHtml(); // render the AST to html
    echo $query->getDocument()->toText(); // echo a text representation of your html

### Search using CSS Selectors

PHPricot supports CSS Selectors to find elements in the AST. A subset of the CSS 3 specification
is supported (excluding namespaces, pseudo elements and classes aswell as the sibling operator ~).

    $query = new PHPRicot_Query($html);
    $links = $query->find('a.selected');

### Add, Remove, or toggle a class

    $query = new PHPRicot_Query($html);
    $query->find('p')->addClass('foo')
                     ->removeClass('bar')
                     ->toggleClass('baz');
    $html = $query->toHtml();

### Access and modify attributes

    $query = new PHPricot_Query($html);
    $val = $query->find('p#foo')->attr('align', 'center')->attr('align');

## TODOS

* Port all the parts of the jQuery API that apply to this use-case
* Add HTML context details (what tags are are really HTML tags)
* Attempt to fix some broken HTML if possible