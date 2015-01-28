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

$item = getLatestItem($feed_url);
