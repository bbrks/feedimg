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
