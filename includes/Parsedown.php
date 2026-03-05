<?php
// includes/Parsedown.php
// A simplified version of Parsedown for this project

class Parsedown {
    // Language detection keywords
    private $languageKeywords = [
        'php' => ['<?php', 'echo', 'print', 'function', 'class', 'interface', 'trait', 'namespace', 'use ', 'require', 'include', 'var ', 'public', 'private', 'protected', 'static', 'const', 'new ', 'isset', 'unset', 'array(', 'json_', 'mysqli_', 'pdo', '$_GET', '$_POST', '$_SESSION', '$_COOKIE', '$_SERVER', '$_FILES', '$_ENV', '$_REQUEST'],
        'javascript' => ['function ', 'const ', 'let ', 'var ', '=>', 'async ', 'await ', 'class ', 'import ', 'export ', 'module.exports', 'require(', 'document.', 'window.', 'console.', 'Promise', 'then(', 'catch(', 'new Promise', 'JSON.', 'localStorage.', 'sessionStorage.'],
        'python' => ['def ', 'class ', 'import ', 'from ', 'return ', 'print(', 'lambda ', 'with ', 'as ', 'try:', 'except:', 'finally:', 'raise ', 'yield ', 'global ', 'nonlocal ', 'self.', '@property', '@staticmethod', '@classmethod', 'if __name__ == "__main__":'],
        'java' => ['public ', 'private ', 'protected ', 'class ', 'interface ', 'extends ', 'implements ', 'import ', 'package ', 'static ', 'final ', 'void ', 'new ', 'System.out.println', 'try {', 'catch (', 'finally {', 'throw ', 'throws ', '@Override', 'public static void main'],
        'csharp' => ['public ', 'private ', 'protected ', 'class ', 'interface ', 'namespace ', 'using ', 'static ', 'void ', 'int ', 'string ', 'bool ', 'double ', 'float ', 'var ', 'new ', 'return ', 'try {', 'catch (', 'finally {', 'throw ', 'async ', 'await ', 'Console.WriteLine', '[Attribute]'],
        'cpp' => ['#include ', 'using namespace ', 'int main(', 'class ', 'public:', 'private:', 'protected:', 'template ', 'namespace ', 'std::', 'cout <<', 'cin >>', 'new ', 'delete ', 'try {', 'catch (', 'throw ', 'virtual ', 'override ', 'constexpr ', 'auto '],
        'c' => ['#include ', 'int main(', 'void ', 'char ', 'int ', 'float ', 'double ', 'struct ', 'typedef ', 'enum ', 'union ', 'malloc(', 'free(', 'printf(', 'scanf(', 'return ', 'try {', 'throw ', 'const ', 'static '],
        'go' => ['package ', 'import ', 'func ', 'type ', 'struct ', 'interface ', 'map[', 'chan ', 'make(', 'new(', 'go ', 'defer ', 'panic(', 'recover(', 'if err != nil {', 'fmt.', 'time.', 'net/http'],
        'rust' => ['fn ', 'let ', 'mut ', 'pub ', 'struct ', 'enum ', 'impl ', 'trait ', 'use ', 'mod ', 'crate ', 'match ', 'loop ', 'while ', 'for ', 'return ', 'panic!', 'println!', 'vec!['],
        'ruby' => ['def ', 'class ', 'module ', 'end', 'require ', 'include ', 'puts ', 'print ', 'lambda ', 'yield ', 'begin ', 'rescue ', 'ensure ', 'raise ', 'attr_reader ', 'attr_writer ', 'attr_accessor '],
        'swift' => ['func ', 'let ', 'var ', 'class ', 'struct ', 'enum ', 'import ', 'protocol ', 'extension ', 'init ', 'return ', 'print(', 'try ', 'catch ', 'throws ', 'async ', 'await ', 'guard '],
        'kotlin' => ['fun ', 'val ', 'var ', 'class ', 'interface ', 'import ', 'package ', 'override ', 'data class ', 'object ', 'companion object ', 'return ', 'println(', 'try {', 'catch (', 'lateinit ', 'const '],
        'typescript' => ['function ', 'const ', 'let ', 'var ', 'class ', 'interface ', 'import ', 'export ', 'type ', 'enum ', 'namespace ', 'module ', 'async ', 'await ', 'Promise', 'then(', 'catch(', 'new Promise', 'Array<', 'Map<'],
        'sql' => ['SELECT ', 'INSERT INTO ', 'UPDATE ', 'DELETE FROM ', 'CREATE TABLE ', 'ALTER TABLE ', 'DROP TABLE ', 'FROM ', 'WHERE ', 'JOIN ', 'INNER JOIN ', 'LEFT JOIN ', 'RIGHT JOIN ', 'FULL JOIN ', 'GROUP BY ', 'ORDER BY ', 'LIMIT ', 'INTO ', 'VALUES ', 'SET ', 'AND ', 'OR ', 'NOT ', 'LIKE ', 'IN ', 'BETWEEN ', 'IS NULL', 'IS NOT NULL'],
        'bash' => ['#!/bin/bash', '#!/bin/sh', 'echo ', 'printf ', 'cd ', 'ls ', 'pwd ', 'mkdir ', 'rm ', 'cp ', 'mv ', 'cat ', 'grep ', 'sed ', 'awk ', 'chmod ', 'chown ', 'export ', 'source ', 'if [', 'then', 'else', 'fi', 'for ', 'while ', 'until ', 'case ', 'function ', 'return '],
        'powershell' => ['Get-', 'Set-', 'New-', 'Remove-', 'Start-', 'Stop-', 'Write-', 'Read-', 'Test-', 'Invoke-', 'Import-', 'Export-', 'Select-', 'Sort-', 'Group-', 'Measure-', 'Where-', 'ForEach-', 'Copy-', 'Move-', 'Rename-', '$', 'param(', 'begin {', 'process {', 'end {', 'function ', 'class ', 'try {', 'catch {', 'finally {', 'throw ', 'return ', 'continue ', 'break '],
        'html' => ['<!DOCTYPE ', '<html', '<head>', '<body>', '<div>', '<span>', '<p>', '<a ', '<img ', '<script>', '<style>', '<link ', '<meta ', '<table>', '<tr>', '<td>', '<ul>', '<li>', '<form ', '<input ', '<button'],
        'css' => ['{', '}', 'html {', 'body {', '.class {', '#id {', '@media ', '@keyframes ', 'background:', 'color:', 'font:', 'margin:', 'padding:', 'border:', 'display:', 'position:', 'width:', 'height:', 'flex:', 'grid:'],
        'json' => ['{', '}', '[', ']', '":', '":{', '": [', '": "', '": true', '": false', '": null', '": ', '":{', '":['],
        'xml' => ['<?xml ', '<!DOCTYPE ', '<', '>', '</', '<!--', '-->', '<![CDATA[', 'xmlns:', 'xsi:', 'xsd:'],
        'yaml' => ['---', 'key:', 'key: ', 'key:\n', '- ', 'true', 'false', 'null', '<<:', '&', '*'],
        'dockerfile' => ['FROM ', 'RUN ', 'COPY ', 'ADD ', 'EXPOSE ', 'ENV ', 'ARG ', 'VOLUME ', 'USER ', 'WORKDIR ', 'CMD ', 'ENTRYPOINT ', 'LABEL ', 'MAINTAINER ', 'HEALTHCHECK ', 'STOPSIGNAL '],
        'nginx' => ['server {', 'location ', 'upstream ', 'listen ', 'server_name ', 'root ', 'index ', 'proxy_pass ', 'fastcgi_pass ', 'rewrite ', 'return ', 'try_files ', 'error_page ', 'access_log ', 'error_log '],
        'git' => ['git ', 'commit', 'push', 'pull', 'branch', 'checkout', 'merge', 'rebase', 'stash', 'diff', 'log', 'status', 'remote', 'fetch', 'tag', 'reset', 'revert'],
    ];

    function text($text) {
        // Simple Markdown Parser Implementation
        
        // 0. Pre-process: Normalize newlines
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = trim($text);
        
        // 0.1. Save HTML/CSS/JS blocks before any processing
        // ```html, ```css, ```js - Render directly with toggle between code and preview
        $html_blocks = [];
        $text = preg_replace_callback('/^```html\s*\n(.*?)```/ms', function($matches) use (&$html_blocks) {
            $id = uniqid('html_');
            $code = htmlspecialchars($matches[1]);
            $preview = $matches[1];
            $html_blocks[$id] = '<div class="code-toggle"><div class="code-toggle-header"><span class="code-toggle-title">HTML</span><div class="code-toggle-buttons"><button class="code-toggle-btn active" data-mode="code">代码</button><button class="code-toggle-btn" data-mode="preview">预览</button></div></div><div class="code-toggle-content code-content"><pre><code class="language-html">' . $code . '</code></pre></div><div class="code-toggle-content preview-content" style="display: none;">' . $preview . '</div></div>';
            return $id;
        }, $text);
        
        $css_blocks = [];
        $text = preg_replace_callback('/^```css\s*\n(.*?)```/ms', function($matches) use (&$css_blocks) {
            $id = uniqid('css_');
            $code = htmlspecialchars($matches[1]);
            $preview = '<style>' . $matches[1] . '</style>';
            $css_blocks[$id] = '<div class="code-toggle"><div class="code-toggle-header"><span class="code-toggle-title">CSS</span><div class="code-toggle-buttons"><button class="code-toggle-btn active" data-mode="code">代码</button><button class="code-toggle-btn" data-mode="preview">预览</button></div></div><div class="code-toggle-content code-content"><pre><code class="language-css">' . $code . '</code></pre></div><div class="code-toggle-content preview-content" style="display: none;">' . $preview . '</div></div>';
            return $id;
        }, $text);
        
        $js_blocks = [];
        $text = preg_replace_callback('/^```javascript\s*\n(.*?)```/ms', function($matches) use (&$js_blocks) {
            $id = uniqid('js_');
            $code = htmlspecialchars($matches[1]);
            $preview = '<script>' . $matches[1] . '</script>';
            $js_blocks[$id] = '<div class="code-toggle"><div class="code-toggle-header"><span class="code-toggle-title">JavaScript</span><div class="code-toggle-buttons"><button class="code-toggle-btn active" data-mode="code">代码</button><button class="code-toggle-btn" data-mode="preview">预览</button></div></div><div class="code-toggle-content code-content"><pre><code class="language-javascript">' . $code . '</code></pre></div><div class="code-toggle-content preview-content" style="display: none;">' . $preview . '</div></div>';
            return $id;
        }, $text);
        
        // Save code blocks for syntax highlighting
        $html_code_blocks = [];
        $text = preg_replace_callback('/^```html-code\s*\n(.*?)```/ms', function($matches) use (&$html_code_blocks) {
            $id = uniqid('html_code_');
            $html_code_blocks[$id] = '<pre><code class="language-html">' . htmlspecialchars($matches[1]) . '</code></pre>';
            return $id;
        }, $text);
        
        $css_code_blocks = [];
        $text = preg_replace_callback('/^```css-code\s*\n(.*?)```/ms', function($matches) use (&$css_code_blocks) {
            $id = uniqid('css_code_');
            $css_code_blocks[$id] = '<pre><code class="language-css">' . htmlspecialchars($matches[1]) . '</code></pre>';
            return $id;
        }, $text);
        
        $js_code_blocks = [];
        $text = preg_replace_callback('/^```js-code\s*\n(.*?)```/ms', function($matches) use (&$js_code_blocks) {
            $id = uniqid('js_code_');
            $js_code_blocks[$id] = '<pre><code class="language-javascript">' . htmlspecialchars($matches[1]) . '</code></pre>';
            return $id;
        }, $text);
        
        // 0.2. Save inline LaTeX formulas
        $inline_latex = [];
        $text = preg_replace_callback('/\$\$(.*?)\$\$/s', function($matches) use (&$inline_latex) {
            $id = uniqid('latex-inline-');
            $inline_latex[$id] = '$$' . $matches[1] . '$$';
            return $id;
        }, $text);
        
        $text = preg_replace_callback('/\$(.*?)\$/s', function($matches) use (&$inline_latex) {
            $id = uniqid('latex-inline-');
            $inline_latex[$id] = '$' . $matches[1] . '$';
            return $id;
        }, $text);

        // 1. Code Blocks (Fenced) - Must be processed BEFORE headers to avoid # in code being parsed as headers
        // Improved regex to handle optional language and newlines better
        $code_blocks = [];
        $text = preg_replace_callback('/^```(\w+)?\s*\n(.*?)```/ms', function($matches) use (&$code_blocks, &$self) {
            $id = uniqid('code_');
            $lang = !empty($matches[1]) ? $matches[1] : 'text';
            $code = $matches[2];
            
            // Detect language if not specified or if it's 'text'
            if ($lang === 'text') {
                $lang = $self->detectLanguage($code);
            }
            
            // Special handling for Mermaid diagrams
            if ($lang === 'mermaid') {
                $code_blocks[$id] = '<div class="mermaid">' . htmlspecialchars($code) . '</div>';
                return $id;
            }
            
            // Note: LaTeX blocks will be handled by KaTeX's auto-render in post.php
            if ($lang === 'latex' || $lang === 'math') {
                $code_blocks[$id] = '<div class="math-block">' . $code . '</div>';
                return $id;
            }
            
            // Regular code block with copy and collapse buttons
            $lineCount = substr_count($code, "\n") + 1;
            $collapseClass = $lineCount > 10 ? ' collapsible' : '';
            $collapsedStyle = $lineCount > 10 ? ' style="max-height: 200px;"' : '';
            
            $code_blocks[$id] = '<pre data-language="' . $lang . '" data-lines="' . $lineCount . '" class="code-block' . $collapseClass . '"><div class="code-header"><span class="code-language">' . htmlspecialchars($lang) . '</span><div class="code-header-actions"><button class="code-btn copy-btn" title="复制"><i class="fas fa-copy"></i></button>' . ($lineCount > 10 ? '<button class="code-btn toggle-btn" title="展开/折叠"><i class="fas fa-chevron-down"></i></button>' : '') . '</div></div><div class="code-content"' . $collapsedStyle . '><div class="line-numbers"></div><code class="language-' . $lang . '">' . htmlspecialchars($code) . '</code></div></pre>';
            return $id;
        }, $text);

        // 2. Headers
        $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
        $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
        $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
        $text = preg_replace('/^#### (.*?)$/m', '<h4>$1</h4>', $text);
        $text = preg_replace('/^##### (.*?)$/m', '<h5>$1</h5>', $text);
        $text = preg_replace('/^###### (.*?)$/m', '<h6>$1</h6>', $text);

        // 3. Bold and Italic
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $text);
        $text = preg_replace('/_(.*?)_/', '<em>$1</em>', $text);

        // 4. Images (before links to avoid conflict)
        $text = preg_replace('/!\[(.*?)\]\((.*?)\)/', '<img src="$2" alt="$1">', $text);

        // 5. Links
        $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" target="_blank">$1</a>', $text);

        // Inline Code
        $text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);
        
        // Note: Inline LaTeX will be handled by KaTeX's auto-render in post.php

        // 6. Blockquotes
        $text = preg_replace('/^> (.*?)$/m', '<blockquote>$1</blockquote>', $text);

        // 7. Horizontal Rule
        $text = preg_replace('/^---$/m', '<hr>', $text);

        // 8. Lists
        // Unordered
        $text = preg_replace('/^\* (.*?)$/m', '<li>$1</li>', $text);
        $text = preg_replace('/^- (.*?)$/m', '<li>$1</li>', $text);
        
        // Wrap adjacent li in ul
        $text = preg_replace_callback('/(<li>.*?<\/li>\n*)+/s', function($matches) {
            return "<ul>\n" . trim($matches[0]) . "\n</ul>";
        }, $text);

        // 9. Tables
        // Find table blocks
        // Pattern: Look for lines that look like table rows, including separator
        $text = preg_replace_callback('/((?:\|.*?\|\s*\n)+)/s', function($matches) {
            $block = trim($matches[0]);
            $lines = explode("\n", $block);
            if (count($lines) < 2) return $matches[0];

            // Check if second line is a separator line (contains only | - : space)
            // Trim whitespace to be safe
            $separator = trim($lines[1]);
            if (!preg_match('/^\|[\s\-:|]+\|$/', $separator)) {
                return $matches[0];
            }

            $html = "<table>\n";
            
            // Header
            $html .= "<thead>\n<tr>\n";
            $headers = array_filter(explode('|', $lines[0]), function($val) { return trim($val) !== ''; });
            foreach ($headers as $header) {
                $html .= "<th>" . trim($header) . "</th>\n";
            }
            $html .= "</tr>\n</thead>\n";

            // Body
            $html .= "<tbody>\n";
            for ($i = 2; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                if (empty($line)) continue;
                
                $html .= "<tr>\n";
                // Explode by pipe, but filter out empty start/end if they exist
                $cells = explode('|', $line);
                
                // Remove first and last empty elements if they exist (common in | data | format)
                if (trim($cells[0]) === '') array_shift($cells);
                if (trim(end($cells)) === '') array_pop($cells);

                foreach ($cells as $cell) {
                    $html .= "<td>" . trim($cell) . "</td>\n";
                }
                $html .= "</tr>\n";
            }
            $html .= "</tbody>\n</table>";

            return $html;
        }, $text);

        // 10. Paragraphs
        // Split by double newlines
        $lines = explode("\n\n", $text);
        $new_text = '';
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Restore code blocks
            if (isset($code_blocks[$line])) {
                $new_text .= $code_blocks[$line] . "\n";
                continue;
            }

            // If it's already an HTML tag (header, blockquote, pre, ul, ol, hr, div, table), don't wrap in p
            // Improved check to handle tags that might have attributes
            if (preg_match('/^<(h[1-6]|blockquote|pre|ul|ol|hr|div|table|img)/', $line)) {
                $new_text .= $line . "\n";
            } else {
                // Only wrap if it doesn't look like a placeholder
                if (!isset($code_blocks[$line])) {
                     $new_text .= "<p>" . nl2br($line) . "</p>\n";
                } else {
                     $new_text .= $code_blocks[$line] . "\n";
                }
            }
        }
        
        // Final restoration of code blocks if they were inside paragraphs (fallback)
        foreach ($code_blocks as $id => $block) {
            $new_text = str_replace($id, $block, $new_text);
        }
        
        // Restore HTML blocks
        foreach ($html_blocks as $id => $block) {
            $new_text = str_replace($id, $block, $new_text);
        }
        
        // Restore CSS blocks
        foreach ($css_blocks as $id => $block) {
            $new_text = str_replace($id, $block, $new_text);
        }
        
        // Restore JS blocks
        foreach ($js_blocks as $id => $block) {
            $new_text = str_replace($id, $block, $new_text);
        }
        
        // Restore inline LaTeX formulas (handle various cases)
        foreach ($inline_latex as $id => $formula) {
            // Direct replacement
            $new_text = str_replace($id, $formula, $new_text);
            // Handle if wrapped in <p> tags
            $new_text = str_replace('<p>' . $id . '</p>', $formula, $new_text);
            // Handle if wrapped in <p> tags with trailing newline
            $new_text = str_replace('<p>' . $id . "</p>\n", $formula . "\n", $new_text);
            // Handle if wrapped in <p> tags with leading newline
            $new_text = str_replace("<p>" . $id . '</p>', $formula, $new_text);
        }

        return $new_text;
    }

    /**
     * Detect programming language from code content
     */
    private function detectLanguage($code, $detectedLang = null) {
        if ($detectedLang && $detectedLang !== 'text') {
            return $detectedLang;
        }
        
        $bestMatch = 'text';
        $maxMatches = 0;
        
        foreach ($this->languageKeywords as $lang => $keywords) {
            $matchCount = 0;
            foreach ($keywords as $keyword) {
                if (stripos($code, $keyword) !== false) {
                    $matchCount++;
                }
            }
            
            if ($matchCount > $maxMatches) {
                $maxMatches = $matchCount;
                $bestMatch = $lang;
            }
        }
        
        // Only return detected language if we found enough matches
        // Minimum threshold: at least 1 keyword match for common languages
        if ($maxMatches >= 1 && $bestMatch !== 'text') {
            return $bestMatch;
        }
        
        return 'text';
    }

    static function instance() {
        return new self();
    }
}
?>
