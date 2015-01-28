<?php

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
    $item['pubDate'] = $x->channel->item[0]->pubDate;

    return $item;

}

function drawFeedImage($item) {
  $text = $item['title'];

  $image = new Imagick();
  $draw = new ImagickDraw();
  $color = new ImagickPixel('#000000');
  $background = new ImagickPixel('none');

  $draw->setFont('./OpenSans-Regular.ttf');
  $draw->setFontSize(24);
  $draw->setFillColor($color);
  $draw->setStrokeAntialias(true);
  $draw->setTextAntialias(true);

  $metrics = $image->queryFontMetrics($draw, $text);

  $draw->annotation(0, $metrics['ascender'], $text);

  $image->newImage($metrics['textWidth'], $metrics['textHeight'], $background);
  $image->setImageFormat('png');
  $image->drawImage($draw);

  header('Content-type: image/png');
  echo $image;
}

$item = getLatestItem($feed_url);
drawFeedImage($item);
