<?php

$xmlFile = __DIR__ . '/../docs/xml/structure.xml';
$outputFile = __DIR__ . '/../DOCUMENTATION.md';

if (!file_exists($xmlFile)) {
    die("Error: structure.xml not found. Run 'php composer docs' first with XML template.\n");
}

$xml = simplexml_load_file($xmlFile);
$md = "# Project Documentation: " . $xml['name'] . "\n\n";

foreach ($xml->file as $file) {
    $filePath = (string)$file['path'];
    
    foreach ($file->class as $class) {
        $className = (string)$class->full_name;
        $description = (string)$class->docblock->description ?: (string)$class->docblock->tag[0]['description'] ?: "No description available.";
        
        $md .= "## Class: `{$className}`\n";
        $md .= "> {$description}\n\n";
        
        if ($class->method) {
            $md .= "### Methods\n\n";
            foreach ($class->method as $method) {
                $methodName = (string)$method->name;
                $visibility = (string)$method['visibility'];
                $methodDesc = (string)$method->docblock->description ?: "No description.";
                
                // Get parameters
                $params = [];
                if ($method->docblock->tag) {
                    foreach ($method->docblock->tag as $tag) {
                        if ((string)$tag['name'] === 'param') {
                            $type = (string)$tag['type'] ?: 'mixed';
                            $var = (string)$tag['variable'] ?: '';
                            $pDesc = (string)$tag['description'] ?: '';
                            $params[] = "`{$type}` **{$var}** {$pDesc}";
                        }
                    }
                }
                
                $md .= "#### `{$visibility} function {$methodName}()`\n";
                $md .= "{$methodDesc}\n\n";
                if ($params) {
                    $md .= "**Parameters:**\n";
                    foreach ($params as $p) {
                        $md .= "- {$p}\n";
                    }
                    $md .= "\n";
                }
                
                // Return value
                $returnTag = null;
                foreach ($method->docblock->tag as $tag) {
                    if ((string)$tag['name'] === 'return') {
                        $returnTag = $tag;
                        break;
                    }
                }
                if ($returnTag) {
                    $md .= "**Returns:** `".(string)$returnTag['type']."` ".(string)$returnTag['description']."\n\n";
                }
                
                $md .= "---\n\n";
            }
        }
        $md .= "\n";
    }
}

file_put_contents($outputFile, $md);
echo "Documentation generated in DOCUMENTATION.md\n";
