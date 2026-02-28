<?php
// includes/Parsedown.php
// A simplified version of Parsedown for this project

class Parsedown {
    function text($text) {
        // Simple Markdown Parser Implementation
        
        // 0. Pre-process: Normalize newlines
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = trim($text);

        // 1. Headers
        $text = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $text);
        $text = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $text);
        $text = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $text);
        $text = preg_replace('/^#### (.*?)$/m', '<h4>$1</h4>', $text);
        $text = preg_replace('/^##### (.*?)$/m', '<h5>$1</h5>', $text);
        $text = preg_replace('/^###### (.*?)$/m', '<h6>$1</h6>', $text);

        // 2. Bold and Italic
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
        $text = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $text);
        $text = preg_replace('/_(.*?)_/', '<em>$1</em>', $text);

        // 3. Images (before links to avoid conflict)
        $text = preg_replace('/!\[(.*?)\]\((.*?)\)/', '<img src="$2" alt="$1">', $text);

        // 4. Links
        $text = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" target="_blank">$1</a>', $text);

        // 5. Code Blocks (Fenced)
        // Improved regex to handle optional language and newlines better
        $code_blocks = [];
        $text = preg_replace_callback('/^```(\w+)?\s*\n(.*?)```/ms', function($matches) use (&$code_blocks) {
            $id = uniqid('code_');
            $lang = !empty($matches[1]) ? $matches[1] : 'text';
            $code = $matches[2];
            // Escape HTML in code block to prevent rendering
            $code_blocks[$id] = '<pre><code class="language-' . $lang . '">' . htmlspecialchars($code) . '</code></pre>';
            return $id;
        }, $text);

        // Inline Code
        $text = preg_replace('/`(.*?)`/', '<code>$1</code>', $text);

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

        return $new_text;
    }

    static function instance() {
        return new self();
    }
}
?>
