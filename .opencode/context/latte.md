# Latte Template Engine v2.3

Latte is a fast and secure template engine for PHP that compiles templates to optimized PHP code. It introduces context-aware escaping to automatically protect against XSS vulnerabilities without manual intervention. The engine features an intuitive syntax familiar to PHP developers, supports template inheritance through blocks and layouts, and provides extensive filtering capabilities for data transformation. Latte compiles templates once and caches them for optimal performance, automatically recompiling when source files change.

This v2.3 branch focuses on core templating functionality with macro-based customization. The engine uses a loader system for template sources (file-based or string-based), compiles templates through a parser and compiler pipeline, and executes cached PHP code with variable escaping based on content type context (HTML, XHTML, XML, JavaScript, CSS, URL, iCal, or plain text). Custom filters can be registered for data transformation, and the macro system allows extending the template language with custom tags and control structures.

## APIs and Key Functions

### Basic Template Rendering

Render templates to output or capture as strings.

```php
<?php

require 'vendor/autoload.php';

$latte = new Latte\Engine;

// Set cache directory for compiled templates
$latte->setTempDirectory(__DIR__ . '/temp');

// Render template directly to output
$latte->render('template.latte', ['title' => 'Hello', 'items' => ['foo', 'bar']]);

// Render template to string
$html = $latte->renderToString('template.latte', ['title' => 'World']);

echo $html;
```

### String Template Loader

Load templates from strings for inline rendering or testing.

```php
<?php

$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

// Render inline template
$result = $latte->renderToString('<h1>{$title}</h1><p>{$content}</p>', [
    'title' => 'Welcome',
    'content' => 'This is a test',
]);

echo $result;
// Output: <h1>Welcome</h1><p>This is a test</p>
```

### File Template Loader

Load templates from filesystem (default loader).

```php
<?php

$latte = new Latte\Engine;

// FileLoader is the default, but you can set it explicitly
$latte->setLoader(new Latte\Loaders\FileLoader);

// Set cache directory
$latte->setTempDirectory(__DIR__ . '/cache');

// Render file-based template
$latte->render('templates/homepage.latte', ['user' => 'John']);

// Templates can include/extend other templates
// {include 'header.latte'}
// {extends 'layout.latte'}
```

### Custom Filters

Add custom filters to transform template output.

```php
<?php

$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

// Add callable as filter
$latte->addFilter('trim', 'trim');
echo $latte->renderToString('{$text|trim}', ['text' => '  hello  ']);
// Output: hello

// Add closure as filter
$latte->addFilter('double', function($val) {
    return $val * 2;
});
echo $latte->renderToString('{$num|double}', ['num' => 21]);
// Output: 42

// Add static method as filter
$latte->addFilter('reverse', 'Latte\Runtime\Filters::reverse');
echo $latte->renderToString('{$str|reverse}', ['str' => 'abc']);
// Output: cba

// Add object method as filter
class Formatter {
    public function currency($value) {
        return '$' . number_format($value, 2);
    }
}
$latte->addFilter('currency', [new Formatter, 'currency']);
echo $latte->renderToString('{$price|currency}', ['price' => 1234.5]);
// Output: $1,234.50

// Dynamic filter (fallback for undefined filters)
$latte->addFilter(NULL, function($filterName, $value) {
    if ($filterName === 'custom') {
        return strtoupper($value);
    }
    return NULL; // Continue to next dynamic filter
});
```

### Invoking Filters Programmatically

Call registered filters from PHP code.

```php
<?php

$latte = new Latte\Engine;
$latte->addFilter('multiply', function($a, $b) {
    return $a * $b;
});

// Invoke filter by name with arguments
$result = $latte->invokeFilter('multiply', [6, 7]);
echo $result;
// Output: 42

// Use built-in filters
$result = $latte->invokeFilter('upper', ['hello']);
echo $result;
// Output: HELLO

// Get all registered filters
$filters = $latte->getFilters();
print_r(array_keys($filters));
```

### Template Compilation and Caching

Compile templates to PHP code and manage cache files.

```php
<?php

$latte = new Latte\Engine;
$latte->setTempDirectory(__DIR__ . '/cache');

// Enable auto-refresh (default: TRUE)
// Recompiles template when source file changes
$latte->setAutoRefresh(TRUE);

// Get compiled PHP code as string
$phpCode = $latte->compile('{$var|upper}');
echo $phpCode;

// Get cache file path for a template
$cacheFile = $latte->getCacheFile('template.latte');
echo $cacheFile;
// Output: /path/to/cache/template-latte-abc123def456.php

// Disable cache (compile on every render, slower)
$latte->setTempDirectory(NULL);
$latte->render('{$test}', ['test' => 'Hello']);
```

### Content Type Configuration

Set content type for context-aware escaping.

```php
<?php

$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

// HTML content (default)
$latte->setContentType(Latte\Engine::CONTENT_HTML);
echo $latte->renderToString('<p>{$text}</p>', ['text' => '<script>alert("XSS")</script>']);
// Output: <p>&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;</p>

// XHTML content
$latte->setContentType(Latte\Engine::CONTENT_XHTML);
echo $latte->renderToString('<p>{$text}</p>', ['text' => '<br>']);
// Output: <p>&lt;br&gt;</p>

// XML content
$latte->setContentType(Latte\Engine::CONTENT_XML);
echo $latte->renderToString('<tag>{$attr}</tag>', ['attr' => 'value & more']);
// Output: <tag>value &amp; more</tag>

// JavaScript content
$latte->setContentType(Latte\Engine::CONTENT_JS);
echo $latte->renderToString('var text = {$val};', ['val' => "hello'world"]);
// Output: var text = "hello\'world";

// CSS content
$latte->setContentType(Latte\Engine::CONTENT_CSS);

// URL content
$latte->setContentType(Latte\Engine::CONTENT_URL);

// iCal content
$latte->setContentType(Latte\Engine::CONTENT_ICAL);

// Plain text (no escaping)
$latte->setContentType(Latte\Engine::CONTENT_TEXT);
echo $latte->renderToString('{$html}', ['html' => '<b>bold</b>']);
// Output: <b>bold</b>
```

### Custom Macros with MacroSet

Extend template language with custom tags using the macro system.

```php
<?php

use Latte\Macros\MacroSet;
use Latte\MacroNode;
use Latte\PhpWriter;

$latte = new Latte\Engine;

// Get compiler and add macro set
$set = new MacroSet($latte->getCompiler());

// Add simple macro
$set->addMacro('hello', function(MacroNode $node, PhpWriter $writer) {
    return $writer->write('echo "Hello from macro!";');
});

// Add pair macro (opening and closing tags)
$set->addMacro('wrapper',
    function(MacroNode $node, PhpWriter $writer) {
        return $writer->write('echo "<div class=\"wrapper\">";');
    },
    function(MacroNode $node, PhpWriter $writer) {
        return $writer->write('echo "</div>";');
    }
);

$latte->setLoader(new Latte\Loaders\StringLoader);

echo $latte->renderToString('{hello}');
// Output: Hello from macro!

echo $latte->renderToString('{wrapper}Content{/wrapper}');
// Output: <div class="wrapper">Content</div>
```

### Control Structures in Templates

Use built-in control flow tags within templates.

```php
<?php

$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

// Conditional rendering
$template = <<<'LATTE'
{if $user}
    Welcome {$user}!
{else}
    Please log in
{/if}
LATTE;

echo $latte->renderToString($template, ['user' => 'John']);
// Output: Welcome John!

// Foreach loop with iterator
$template = <<<'LATTE'
{foreach $items as $item}
    {$iterator->counter}. {$item}
    {if !$iterator->last}, {/if}
{/foreach}
LATTE;

echo $latte->renderToString($template, ['items' => ['apple', 'banana', 'cherry']]);
// Output: 1. apple, 2. banana, 3. cherry

// While loop
$template = '{var $i = 0}{while $i < 3}{$i++}, {/while}';
echo $latte->renderToString($template);
// Output: 0, 1, 2,

// For loop
$template = '{for $i = 0; $i < 3; $i++}{$i}, {/for}';
echo $latte->renderToString($template);
// Output: 0, 1, 2,
```

### Template Blocks and Inheritance

Define reusable blocks and extend layouts.

```php
<?php

$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

// Simulate file templates with string loader
$templates = [
    'layout.latte' => <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>{block title}Default Title{/block}</title>
</head>
<body>
    <header>{block header}Header{/block}</header>
    <main>{block content}Content{/block}</main>
    <footer>{block footer}Footer{/block}</footer>
</body>
</html>
HTML,

    'page.latte' => <<<'HTML'
{extends "layout.latte"}

{block title}My Page{/block}

{block content}
    <h1>Welcome</h1>
    <p>This is the page content</p>
{/block}

{block footer}
    {include parent}
    <p>Copyright 2025</p>
{/block}
HTML,
];

// Note: StringLoader in v2.3 doesn't support named templates by default
// For file-based templates with inheritance, use FileLoader
$latte->setLoader(new Latte\Loaders\FileLoader);
$latte->render('page.latte');
// Outputs full HTML with inheritance applied

// Standalone block definition
$template = <<<'LATTE'
{define blockName}
    This is a reusable block
{/define}

{include blockName}
LATTE;

echo $latte->renderToString($template);
```

### Built-in Filters

Use standard filters for data transformation.

```php
<?php

$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

// String filters
echo $latte->renderToString('{$text|upper}', ['text' => 'hello']);
// Output: HELLO

echo $latte->renderToString('{$text|lower}', ['text' => 'HELLO']);
// Output: hello

echo $latte->renderToString('{$text|capitalize}', ['text' => 'hello world']);
// Output: Hello World

echo $latte->renderToString('{$text|firstUpper}', ['text' => 'hello']);
// Output: Hello

echo $latte->renderToString('{$text|truncate:10,"..."}', ['text' => 'Long text here']);
// Output: Long te...

// Array/collection filters
echo $latte->renderToString('{$arr|implode:", "}', ['arr' => ['a', 'b', 'c']]);
// Output: a, b, c

// Formatting filters
echo $latte->renderToString('{$num|number:2}', ['num' => 1234.5678]);
// Output: 1,234.57

echo $latte->renderToString('{$date|date:"Y-m-d"}', ['date' => time()]);
// Output: 2026-01-02

// Escaping filters (context-specific)
echo $latte->renderToString('{$js|escapeJs}', ['js' => "alert('test')"]);
echo $latte->renderToString('{$css|escapeCss}', ['css' => 'value']);
echo $latte->renderToString('{$url|escapeUrl}', ['url' => 'hello world']);

// HTML manipulation
echo $latte->renderToString('{$text|nl2br}', ['text' => "Line 1\nLine 2"]);
// Output: Line 1<br>Line 2

echo $latte->renderToString('{$html|striptags}', ['html' => '<b>Bold</b>']);
// Output: Bold
```

### n:attributes Syntax

Apply macros as HTML attributes for cleaner templates.

```php
<?php

$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

// n:if attribute
$template = '<p n:if="$show">Visible content</p>';
echo $latte->renderToString($template, ['show' => TRUE]);
// Output: <p>Visible content</p>

// n:foreach attribute
$template = '<li n:foreach="$items as $item">{$item}</li>';
echo $latte->renderToString($template, ['items' => ['a', 'b', 'c']]);
// Output: <li>a</li><li>b</li><li>c</li>

// n:class attribute (conditional classes)
$template = '<div n:class="$active ? active">Content</div>';
echo $latte->renderToString($template, ['active' => TRUE]);
// Output: <div class="active">Content</div>

// n:inner-foreach (apply to element contents only)
$template = '<ul n:inner-foreach="$items as $item"><li>{$item}</li></ul>';
echo $latte->renderToString($template, ['items' => ['x', 'y']]);
// Output: <ul><li>x</li><li>y</li></ul>

// n:tag-if (render tag conditionally)
$template = '<a href="{$url}" n:tag-if="$url">Title</a>';
echo $latte->renderToString($template, ['url' => '']);
// Output: Title (no anchor tag)
```

### Compile-time Callbacks

Register callbacks that execute during template compilation.

```php
<?php

$latte = new Latte\Engine;
$latte->setLoader(new Latte\Loaders\StringLoader);

// Add compile-time callback
$latte->onCompile[] = function($engine) {
    // Add custom filter during compilation
    $engine->addFilter('timestamp', 'time');

    // Access compiler for advanced customization
    $compiler = $engine->getCompiler();

    echo "Template is being compiled\n";
};

// Compile template (triggers callback)
$code = $latte->compile('{$var}');

// Callbacks are cleared after compilation
// Add new callback for next compilation
$latte->onCompile[] = function($engine) {
    echo "Another compilation\n";
};

$latte->render('{$test}', ['test' => 'Hello']);
```

### Parser and Compiler Access

Access low-level parser and compiler for advanced customization.

```php
<?php

$latte = new Latte\Engine;

// Get parser instance
$parser = $latte->getParser();
// Parser can be used to tokenize templates

// Get compiler instance
$compiler = $latte->getCompiler();
// Compiler manages macro sets and code generation

// Add macros via compiler
$compiler->addMacro('custom', new MyMacroImplementation());

// CoreMacros and BlockMacros are installed by default
// They provide standard tags like {if}, {foreach}, {block}, etc.

$latte->setLoader(new Latte\Loaders\StringLoader);
$latte->render('{$test}', ['test' => 'Hello']);
```

## Use Cases and Integration

Latte v2.3 excels in PHP web applications requiring secure template rendering with minimal configuration. Its automatic context-aware escaping makes it ideal for projects where XSS protection is critical, particularly when rendering user-generated content or dynamic HTML. The engine integrates seamlessly with the Nette Framework but works standalone in any PHP 5.3.1+ environment. Common use cases include rendering HTML pages, generating email templates in multiple formats, creating XML/RSS feeds with proper escaping, and building content management systems where templates need compilation caching for performance.

The macro system and filter extensibility make Latte suitable for domain-specific templating needs. Integration patterns typically involve initializing the engine with a temp directory for caching, registering custom filters for business logic, and optionally adding custom macros for specialized tags. The FileLoader handles template inheritance and includes naturally, while StringLoader enables runtime template generation and testing. Template compilation happens once per source change, with auto-refresh detecting modifications in development. The n:attribute syntax keeps templates clean and designer-friendly, making Latte a practical choice for projects where template maintainability and security are priorities.
