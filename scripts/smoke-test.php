<?php
/**
 * Read-only HTTP checks: ddev exec php scripts/smoke-test.php
 * Optionally pass a text file containing additional public URL paths.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

$base = getenv('DDEV_PRIMARY_URL') ?: 'https://pinkfloydlegacy11.ddev.site';
$client = new \GuzzleHttp\Client(['base_uri' => $base, 'http_errors' => FALSE, 'timeout' => 30]);
$paths = ['/', '/user/login', '/shows', '/news', '/contact'];
if (isset($argv[1])) {
  $paths = array_merge($paths, file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
}
$failed = [];
$assets = [];
foreach (array_unique($paths) as $path) {
  $response = $client->get($path);
  $status = $response->getStatusCode();
  echo "$status $path" . PHP_EOL;
  if ($status !== 200) {
    $failed[] = "$status $path";
    continue;
  }
  $html = (string) $response->getBody();
  $dom = new \DOMDocument();
  @$dom->loadHTML($html);
  $xpath = new \DOMXPath($dom);
  foreach ($xpath->query('//script[@src]/@src | //link[@rel="stylesheet"]/@href') as $attribute) {
    $url = $attribute->value;
    if (str_starts_with($url, '/')) {
      $assets[$url] = TRUE;
    }
    elseif (str_starts_with($url, $base . '/')) {
      $assets[substr($url, strlen($base))] = TRUE;
    }
  }
}
foreach (array_keys($assets) as $url) {
  $status = $client->get($url)->getStatusCode();
  if ($status !== 200) {
    $failed[] = "$status $url";
    echo "FAIL $status $url" . PHP_EOL;
  }
}
echo count($assets) . ' unique local CSS/JS assets checked.' . PHP_EOL;
if ($failed) {
  fwrite(STDERR, implode(PHP_EOL, $failed) . PHP_EOL);
  exit(1);
}
echo 'All HTTP checks passed.' . PHP_EOL;
