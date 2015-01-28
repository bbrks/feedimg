<?php

date_default_timezone_set('UTC');

if (isset($_GET['url'])) {
  $feed_url = $_GET['url'];
  // Prepend a protocol if none exists
  if (!preg_match("~^https?://~i", $feed_url)) {
    $feed_url = "http://" . $feed_url;
  }
} else {
  // We can't continue without a feed, so die()
  die('Please supply a URL parameter: ?url=http://example.com/feed.atom');
}

function getLatestItem($feed_url) {

    $content = file_get_contents($feed_url);
    $x = new SimpleXmlElement($content);

    $item['title'] = $x->channel->item[0]->title;
    $item['description'] = $x->channel->item[0]->description;
    $item['link'] = $x->channel->item[0]->link;
    $date = $x->channel->item[0]->pubDate;
    $date = DateTime::createFromFormat('D, d M Y H:i:s T', $date);
    $item['pubDate'] = $date->format('Y-m-d H:i:s');

    return $item;

}

function drawFeedImage($item) {
  $title = $item['title'];
  $date  = $item['pubDate'];

  $width  = 0;
  $height = 0;

  $text[] = $title;
  $text[] = $date;

  $image = new Imagick();
  $draw  = new ImagickDraw();
  $color = new ImagickPixel('#000000');
  $background = new ImagickPixel('none');

  $draw->setFont('./OpenSans-Regular.ttf');
  $draw->setFontSize(24);
  $draw->setFillColor($color);
  $draw->setStrokeAntialias(true);
  $draw->setTextAntialias(true);

  $count = 0;
  foreach ($text as $line) {
    $count++;
    $metrics = $image->queryFontMetrics($draw, $line);
    $draw->annotation(0, $metrics['ascender'] * $count, $line);

    if ($width < $metrics['textWidth']) {
      $width = $metrics['textWidth'];
    }

    if ($height < $metrics['textHeight'] * $count) {
      $height = $metrics['textHeight'] * $count;
    }

  }

  $image->newImage($width, $height, $background);
  $image->setImageFormat('png');
  $image->drawImage($draw);

  header('Content-type: image/png');
  header('Cache-Control: no-cache');
  header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
  echo $image;
}

$item = getLatestItem($feed_url);
drawFeedImage($item);
