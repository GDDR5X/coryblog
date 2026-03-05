/**
 * Simple Syntax Highlighter
 * Safe implementation that mimics PrismJS behavior
 */

document.addEventListener('DOMContentLoaded', () => {
    highlightAll();
    fixCopyButtons();
    addLineNumbers();
    addCollapseButtons();
});

function highlightAll() {
    // 处理普通代码块 (pre > code)，排除 .code-toggle 中的
    const codes = document.querySelectorAll('pre:not(.code-toggle pre) code');
    codes.forEach(code => {
        const classes = code.className;
        const langMatch = classes.match(/language-(\w+)/);
        const lang = langMatch ? langMatch[1] : 'text';
        
        // Add data-language attribute to parent pre for styling
        code.parentElement.setAttribute('data-language', lang);

        if (lang === 'text') return;

        const text = code.textContent;
        code.innerHTML = highlight(text, lang);
    });
    
    // 处理带预览切换的代码块 (.code-toggle .code-content pre code)
    const toggleCodes = document.querySelectorAll('.code-toggle .code-content pre code');
    toggleCodes.forEach(code => {
        const classes = code.className;
        const langMatch = classes.match(/language-(\w+)/);
        const lang = langMatch ? langMatch[1] : 'text';
        
        // Add data-language attribute to parent pre for styling
        code.parentElement.setAttribute('data-language', lang);

        if (lang === 'text') return;

        const text = code.textContent;
        code.innerHTML = highlight(text, lang);
    });
}

function fixCopyButtons() {
    const preBlocks = document.querySelectorAll('pre');
    
    preBlocks.forEach(pre => {
        // Check if copy button already exists in header
        const existingButton = pre.querySelector('.copy-btn');
        if (existingButton) return;
        
        // Create button
        const button = document.createElement('button');
        button.className = 'copy-btn';
        button.innerHTML = '<i class="far fa-copy"></i>';
        button.title = 'Copy to clipboard';
        
        // Add click event
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const code = pre.querySelector('code');
            if (!code) return;
            
            const text = code.textContent;
            
            navigator.clipboard.writeText(text).then(() => {
                const originalHtml = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i>';
                button.classList.add('copied');
                
                setTimeout(() => {
                    button.innerHTML = originalHtml;
                    button.classList.remove('copied');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy text: ', err);
                button.innerHTML = '<i class="fas fa-times"></i>';
            });
        });
        
        // Find or create header for button
        let headerActions = pre.querySelector('.code-header-actions');
        if (!headerActions) {
            headerActions = pre.querySelector('.code-header');
            if (headerActions) {
                headerActions.appendChild(button);
            }
        } else {
            headerActions.appendChild(button);
        }
    });
}

function addLineNumbers() {
    const codes = document.querySelectorAll('pre code');
    
    codes.forEach(code => {
        const text = code.textContent;
        const lines = text.split('\n');
        
        if (lines.length <= 1 && text.trim() === '') return;
        
        let lineNumbers = '';
        for (let i = 1; i <= lines.length; i++) {
            lineNumbers += `<div class="line-number">${i}</div>`;
        }
        
        const lineNumbersDiv = code.previousElementSibling;
        if (lineNumbersDiv && lineNumbersDiv.classList.contains('line-numbers')) {
            lineNumbersDiv.innerHTML = lineNumbers;
        }
    });
}

function addCollapseButtons() {
    const preBlocks = document.querySelectorAll('pre');
    
    preBlocks.forEach(pre => {
        const codeContent = pre.querySelector('.code-content');
        if (!codeContent) return;
        
        const code = codeContent.querySelector('code');
        if (!code) return;
        
        const text = code.textContent;
        const lines = text.split('\n');
        
        if (lines.length <= 5) return;
        
        const collapseBtn = document.createElement('button');
        collapseBtn.className = 'collapse-btn';
        collapseBtn.innerHTML = '<i class="fas fa-chevron-down"></i> 折叠';
        collapseBtn.title = '展开/折叠代码';
        
        collapseBtn.addEventListener('click', () => {
            const isCollapsed = pre.classList.contains('collapsed');
            
            if (isCollapsed) {
                pre.classList.remove('collapsed');
                collapseBtn.innerHTML = '<i class="fas fa-chevron-down"></i> 折叠';
                codeContent.style.display = 'flex';
            } else {
                pre.classList.add('collapsed');
                collapseBtn.innerHTML = '<i class="fas fa-chevron-right"></i> 展开 (共' + lines.length + '行)';
                codeContent.style.display = 'none';
            }
        });
        
        let headerActions = pre.querySelector('.code-header-actions');
        if (!headerActions) {
            headerActions = pre.querySelector('.code-header');
        }
        if (headerActions) {
            headerActions.appendChild(collapseBtn);
        }
    });
}

function highlight(text, lang) {
    let output = '';
    let i = 0;
    
    // 语言特定的关键词定义
    const languageKeywords = {
        'javascript': new Set([
            'function', 'return', 'if', 'else', 'for', 'while', 'var', 'let', 'const', 
            'class', 'import', 'from', 'export', 'default', 'try', 'catch', 'finally',
            'async', 'await', 'new', 'this', 'extends', 'super', 'static', 'get', 'set',
            'typeof', 'instanceof', 'in', 'of', 'void', 'delete', 'yield', 'throw',
            'true', 'false', 'null', 'undefined', 'NaN', 'Infinity',
            'console', 'document', 'window', 'Math', 'JSON', 'Array', 'Object', 'String',
            'Number', 'Boolean', 'Date', 'RegExp', 'Promise', 'Set', 'Map', 'WeakMap',
            'alert', 'confirm', 'prompt', 'setTimeout', 'setInterval', 'clearTimeout',
            'addEventListener', 'removeEventListener', 'querySelector', 'querySelectorAll',
            'getElementById', 'getElementsByClassName', 'getElementsByTagName',
            'createElement', 'appendChild', 'removeChild', 'innerHTML', 'textContent',
            'style', 'classList', 'add', 'remove', 'toggle', 'contains'
        ]),
        'python': new Set([
            'def', 'return', 'if', 'elif', 'else', 'for', 'while', 'try', 'except',
            'finally', 'with', 'as', 'import', 'from', 'class', 'pass', 'break',
            'continue', 'lambda', 'yield', 'raise', 'assert', 'del', 'global',
            'nonlocal', 'print', 'input', 'len', 'range', 'enumerate', 'zip',
            'map', 'filter', 'reduce', 'sum', 'min', 'max', 'abs', 'round',
            'int', 'float', 'str', 'list', 'dict', 'tuple', 'set', 'bool',
            'True', 'False', 'None', 'and', 'or', 'not', 'in', 'is', 'self',
            'super', 'init', 'main', 'name', 'doc', 'repr', 'str', 'call'
        ]),
        'php': new Set([
            'function', 'return', 'if', 'else', 'elseif', 'for', 'while', 'foreach',
            'try', 'catch', 'finally', 'throw', 'class', 'public', 'private',
            'protected', 'static', 'abstract', 'final', 'interface', 'trait',
            'namespace', 'use', 'extends', 'implements', 'new', 'this', 'self',
            'parent', 'echo', 'print', 'var', 'const', 'define', 'include',
            'include_once', 'require', 'require_once', 'array', 'isset', 'unset',
            'empty', 'null', 'true', 'false', 'global', 'static', 'and', 'or',
            'xor', 'as', 'switch', 'case', 'default', 'break', 'continue',
            'goto', 'die', 'exit', 'eval', 'isset', 'unset', 'list', 'each'
        ]),
        'html': new Set([
            'div', 'span', 'p', 'a', 'img', 'script', 'style', 'link', 'meta',
            'head', 'body', 'html', 'title', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'ul', 'ol', 'li', 'table', 'tr', 'td', 'th', 'thead', 'tbody',
            'form', 'input', 'button', 'select', 'option', 'textarea', 'label',
            'br', 'hr', 'strong', 'em', 'b', 'i', 'u', 's', 'strike', 'sub',
            'sup', 'code', 'pre', 'blockquote', 'iframe', 'nav', 'header',
            'footer', 'section', 'article', 'aside', 'main', 'figure', 'figcaption'
        ]),
        'css': new Set([
            'color', 'background', 'background-color', 'background-image',
            'font', 'font-size', 'font-family', 'font-weight', 'font-style',
            'margin', 'padding', 'border', 'width', 'height', 'display',
            'position', 'top', 'left', 'right', 'bottom', 'float', 'clear',
            'overflow', 'visibility', 'z-index', 'opacity', 'cursor',
            'text-align', 'text-decoration', 'text-transform', 'line-height',
            'vertical-align', 'white-space', 'word-wrap', 'word-break',
            'box-shadow', 'text-shadow', 'border-radius', 'transform',
            'transition', 'animation', 'flex', 'grid', 'justify-content',
            'align-items', 'flex-direction', 'flex-wrap', 'gap', 'media',
            'keyframes', 'import', 'charset', 'namespace', 'supports',
            'hover', 'active', 'focus', 'visited', 'first-child', 'last-child',
            'nth-child', 'not', 'is', 'where', 'has', 'before', 'after'
        ]),
        'sql': new Set([
            'SELECT', 'FROM', 'WHERE', 'INSERT', 'INTO', 'VALUES', 'UPDATE',
            'SET', 'DELETE', 'CREATE', 'TABLE', 'ALTER', 'DROP', 'INDEX',
            'JOIN', 'INNER', 'LEFT', 'RIGHT', 'FULL', 'OUTER', 'ON', 'AS',
            'AND', 'OR', 'NOT', 'NULL', 'IS', 'IN', 'BETWEEN', 'LIKE',
            'GROUP', 'BY', 'ORDER', 'HAVING', 'LIMIT', 'OFFSET', 'UNION',
            'ALL', 'DISTINCT', 'COUNT', 'SUM', 'AVG', 'MAX', 'MIN',
            'PRIMARY', 'KEY', 'FOREIGN', 'REFERENCES', 'UNIQUE', 'CHECK',
            'DEFAULT', 'AUTO_INCREMENT', 'CASCADE', 'RESTRICT'
        ]),
        'bash': new Set([
            'if', 'then', 'else', 'elif', 'fi', 'for', 'while', 'do', 'done',
            'case', 'esac', 'in', 'function', 'return', 'exit', 'break',
            'continue', 'shift', 'source', 'export', 'unset', 'readonly',
            'local', 'declare', 'typeset', 'echo', 'printf', 'read', 'test',
            'cd', 'pwd', 'ls', 'cat', 'grep', 'sed', 'awk', 'cut', 'sort',
            'uniq', 'wc', 'head', 'tail', 'find', 'xargs', 'chmod', 'chown',
            'mkdir', 'rm', 'cp', 'mv', 'touch', 'tar', 'gzip', 'gunzip'
        ])
    };
    
    // PowerShell specific definitions
    const powershellKeywords = new Set([
        'if', 'else', 'elseif', 'for', 'foreach', 'while', 'do', 'until', 
        'switch', 'try', 'catch', 'finally', 'throw', 'return', 'break', 
        'continue', 'exit', 'param', 'begin', 'process', 'end', 'filter',
        'function', 'script', 'class', 'enum', 'interface', 'in', 'is', 
        'as', 'and', 'or', 'not', 'band', 'bor', 'bxor', 'shl', 'shr',
        'foreach', 'parallel', 'sequence', 'dynamicparam', 'begin', 'end',
        'data', 'throw', 'trap', 'validation', 'arg', 'argument'
    ]);
    
    const powershellCmdlets = new Set([
        'Get', 'Set', 'New', 'Remove', 'Start', 'Stop', 'Restart', 'Pause', 
        'Resume', 'Invoke', 'Test', 'Convert', 'Format', 'Export', 'Import',
        'Select', 'Sort', 'Group', 'Measure', 'Where', 'ForEach', 'Copy',
        'Move', 'Rename', 'Register', 'Unregister', 'Enable', 'Disable',
        'Write', 'Read', 'Open', 'Close', 'Clear', 'Complete', 'Confirm',
        'Find', 'Filter', 'Gate', 'Grant', 'Hide', 'Initialize', 'Install',
        'Join', 'Lock', 'Mount', 'Move', 'Open', 'Optimize', 'Pop', 'Push',
        'Redo', 'Reinstall', 'Remove', 'Reset', 'Resize', 'Resolve', 'Restore',
        'Restrict', 'Revoke', 'Save', 'Search', 'Select', 'Send', 'Set',
        'Show', 'Skip', 'Sleep', 'Split', 'Start', 'Step', 'Stop', 'Submit',
        'Suspend', 'Switch', 'Sync', 'Test', 'Trace', 'Uninstall', 'Unlock',
        'Unmount', 'Unregister', 'Update', 'Use', 'Wait', 'Watch', 'Write'
    ]);
    
    // 通用关键词（作为后备）
    const genericKeywords = new Set([
        'function', 'return', 'if', 'else', 'for', 'while', 'var', 'let', 'const', 
        'class', 'import', 'from', 'try', 'catch', 'async', 'await', 'echo', 
        'public', 'private', 'protected', 'static', 'new', 'this', 'extends', 
        'implements', 'interface', 'package', 'use', 'namespace', 'include', 
        'require', 'null', 'true', 'false', 'void', 'int', 'string', 'bool',
        'foreach', 'as', 'switch', 'case', 'break', 'default', 'continue',
        'struct', 'union', 'enum', 'typedef', 'sizeof', 'do'
    ]);
    
    // 获取当前语言的关键词集合
    const currentKeywords = languageKeywords[lang] || genericKeywords;
    
    // Check if this is PowerShell
    const isPowershell = lang === 'powershell' || lang === 'ps1' || lang === 'ps';

    while (i < text.length) {
        let remaining = text.substring(i);
        
        // 1. Comments
        // Single line // or # (PHP/Python)
        if (remaining.startsWith('//') || remaining.startsWith('#')) {
            const end = remaining.indexOf('\n');
            const comment = (end === -1) ? remaining : remaining.substring(0, end);
            output += `<span class="token comment">${escapeHtml(comment)}</span>`;
            i += comment.length;
            continue;
        }
        
        // Block Comments /* ... */
        if (remaining.startsWith('/*')) {
            const end = remaining.indexOf('*/');
            const comment = (end === -1) ? remaining : remaining.substring(0, end + 2);
            output += `<span class="token comment">${escapeHtml(comment)}</span>`;
            i += comment.length;
            continue;
        }

        // 2. Strings
        if (remaining.startsWith('"') || remaining.startsWith("'")) {
            const quote = remaining[0];
            let end = -1;
            let escaped = false;
            // Find closing quote, ignoring escaped ones
            for (let j = 1; j < remaining.length; j++) {
                if (remaining[j] === '\\') {
                    escaped = !escaped;
                } else if (remaining[j] === quote && !escaped) {
                    end = j;
                    break;
                } else {
                    escaped = false;
                }
            }
            
            if (end !== -1) {
                const string = remaining.substring(0, end + 1);
                output += `<span class="token string">${escapeHtml(string)}</span>`;
                i += string.length;
                continue;
            }
        }

        // 3. HTML/PHP Tags
        if ((lang === 'html' || lang === 'xml' || lang === 'php') && remaining.startsWith('<')) {
            // PHP Tags
            if (remaining.startsWith('<?php') || remaining.startsWith('<?') || remaining.startsWith('?>')) {
                 const match = remaining.match(/^<\?php|<\?|\?>/);
                 if (match) {
                     output += `<span class="token keyword">${escapeHtml(match[0])}</span>`;
                     i += match[0].length;
                     continue;
                 }
            }
            
            // HTML Tags
            const tagMatch = remaining.match(/^<\/?\w+[^>]*>/);
            if (tagMatch) {
                // Tokenize inside tag? For now, just color the whole tag
                output += `<span class="token tag">${escapeHtml(tagMatch[0])}</span>`;
                i += tagMatch[0].length;
                continue;
            }
        }

        // 4. Numbers
        const numMatch = remaining.match(/^\d+(\.\d+)?/);
        if (numMatch) {
            output += `<span class="token number">${numMatch[0]}</span>`;
            i += numMatch[0].length;
            continue;
        }

        // 5. Keywords & Identifiers
        const wordMatch = remaining.match(/^[a-zA-Z_$]\w*/);
        if (wordMatch) {
            const word = wordMatch[0];
            
            // PowerShell specific highlighting
            if (isPowershell) {
                // Check for cmdlets (Verb-Noun format)
                if (word.match(/^[A-Z][a-z]+-[A-Z][a-z]+/)) {
                    output += `<span class="token cmdlet">${word}</span>`;
                } else if (powershellKeywords.has(word)) {
                    output += `<span class="token keyword">${word}</span>`;
                } else if (powershellCmdlets.has(word)) {
                    output += `<span class="token command">${word}</span>`;
                } else if (word.startsWith('$')) {
                    output += `<span class="token variable">${word}</span>`;
                } else {
                    output += escapeHtml(word);
                }
            } else {
                // 使用语言特定的关键词集合
                if (currentKeywords.has(word)) {
                    output += `<span class="token keyword">${word}</span>`;
                } else if (remaining.substring(word.length).trim().startsWith('(')) {
                    output += `<span class="token function-name">${word}</span>`;
                } else {
                    // Just text/variable
                    output += escapeHtml(word);
                }
            }
            i += word.length;
            continue;
        }

        // 6. Punctuation / Operators
        if (/[{}[\];(),.:]/.test(remaining[0])) {
             output += `<span class="token punctuation">${escapeHtml(remaining[0])}</span>`;
             i++;
             continue;
        }
        
        // Default: just append escaped char
        output += escapeHtml(remaining[0]);
        i++;
    }

    return output;
}

function escapeHtml(unsafe) {
    return unsafe
         .replace(/&/g, "&amp;")
         .replace(/</g, "&lt;")
         .replace(/>/g, "&gt;")
         .replace(/"/g, "&quot;")
         .replace(/'/g, "&#039;");
}
