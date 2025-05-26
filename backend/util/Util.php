<?php


function get_input_json(): ?array
{
  $data = null;
  if ($_SERVER['CONTENT_TYPE'] === 'application/json') {
    $data = get_input_json();
  } else {
    $data = $_POST;
  }
  return $data;
}

function sentence_case($string)
{
  $string = strtolower($string);
  return ucfirst($string);
}

function has_required_keys(?array $data, array $keys): bool
{
  if (!$data) {
    return false;
  }

  foreach ($keys as $key) {
    if (!array_key_exists($key, $data)) {
      return false;
    }
    if (empty($data[$key])) {
      return false;
    }
  }
  return true;
}

function gen_slug(string $title): string
{
  $slug = strtolower($title);
  $slug = trim($slug);
  return str_replace(' ', '-', $slug);
}
