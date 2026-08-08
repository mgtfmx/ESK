<?php
// Path to your video file
$filePath = 'video.mp4'; 

if (!file_exists($filePath)) {
    header("HTTP/1.1 404 Not Found");
    exit("File not found.");
}

$size = filesize($filePath);
$fp = @fopen($filePath, 'rb');
if (!$fp) {
    header("HTTP/1.1 500 Internal Server Error");
    exit("Cannot open file.");
}

$start = 0;
$end = $size - 1;

// Clear any previous output buffering
if (ob_get_level()) ob_end_clean();

// Handle HTTP Range header (crucial for skipping ahead in the video)
if (isset($_SERVER['HTTP_RANGE'])) {
    $c_start = $start;
    $c_end = $end;

    list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
    if (strpos($range, ',') !== false) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    
    if ($range == '-') {
        $c_start = $size - substr($range, 1);
    } else {
        $range = explode('-', $range);
        $c_start = $range[0];
        
        $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size - 1;
    }
    $c_end = ($c_end > $end) ? $end : $c_end;
    
    if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        header("Content-Range: bytes $start-$end/$size");
        exit;
    }
    $start = $c_start;
    $end = $c_end;
    $length = $end - $start + 1;
    fseek($fp, $start);
    header('HTTP/1.1 206 Partial Content');
} else {
    $length = $size;
}

// Send standard video headers
header("Content-Range: bytes $start-$end/$size");
header("Accept-Ranges: bytes");
header("Content-Length: " . $length);
header("Content-Type: video/mp4");

// Stream the file in small chunks to prevent memory bloat
$buffer = 1024 * 8; // 8KB chunks
while (!feof($fp) && ($p = ftell($fp)) <= $end) {
    if ($p + $buffer > $end) {
        $buffer = $end - $p + 1;
    }
    set_time_limit(0); 
    echo fread($fp, $buffer);
    flush();
}

fclose($fp);
exit;