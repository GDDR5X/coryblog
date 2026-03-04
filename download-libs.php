<?php
// download-libs.php
// Download third-party libraries locally

$libs_dir = __DIR__ . '/assets/libs';
if (!file_exists($libs_dir)) {
    mkdir($libs_dir, 0755, true);
}

function downloadFile($url, $filename) {
    echo "Downloading from: $url\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "HTTP Code: $httpCode\n";
    if ($error) {
        echo "Curl Error: $error\n";
    }
    
    if ($httpCode == 200 && $content) {
        file_put_contents($filename, $content);
        return true;
    }
    return false;
}

echo "Downloading ABCjs...\n";
if (downloadFile('https://github.com/paulrosenzweig/abcjs/releases/latest/download/abcjs-min.js', $libs_dir . '/abcjs-min.js')) {
    echo "ABCjs downloaded successfully.\n";
} else {
    echo "ABCjs download failed.\n";
}

echo "Downloading Mermaid...\n";
if (downloadFile('https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js', $libs_dir . '/mermaid.min.js')) {
    echo "Mermaid downloaded successfully.\n";
} else {
    echo "Mermaid download failed.\n";
}

echo "Downloading KaTeX...\n";
if (downloadFile('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js', $libs_dir . '/katex.min.js')) {
    echo "KaTeX downloaded successfully.\n";
} else {
    echo "KaTeX download failed.\n";
}

echo "Downloading KaTeX Auto-render...\n";
if (downloadFile('https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/contrib/auto-render.min.js', $libs_dir . '/auto-render.min.js')) {
    echo "KaTeX Auto-render downloaded successfully.\n";
} else {
    echo "KaTeX Auto-render download failed.\n";
}

echo "All downloads completed.\n";
?>
